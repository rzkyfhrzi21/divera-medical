<?php
require_once '../../config/koneksi.php';

$id_pengguna = $_SESSION['user_id'];
$query_dokter = mysqli_query($koneksi, "SELECT id FROM dokter WHERE id_pengguna='$id_pengguna'");
$data_dokter = mysqli_fetch_assoc($query_dokter);
$id_dokter = $data_dokter['id'];

// Query untuk mengambil data riwayat pasien dokter ini
$query_pasien = "
    SELECT 
        j.id as id_janji,
        j.tanggal_janji,
        j.gejala,
        j.catatan,
        j.status,
        p.id as id_pasien,
        p.tanggal_lahir,
        p.jenis_kelamin,
        p.berat_badan,
        p.alamat,
        p.bpjs,
        u.nama,
        u.telpon
    FROM janji_temu j
    JOIN pasien p ON j.id_pasien = p.id
    JOIN pengguna u ON p.id_pengguna = u.id
    WHERE j.id_dokter = '$id_dokter'
    ORDER BY j.tanggal_janji DESC
";
$result_pasien = mysqli_query($koneksi, $query_pasien);
?>

<div class="content-card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Riwayat Pasien Saya</h5>
            <p class="text-muted small mb-0">Daftar rekam medis dan catatan pemeriksaan pasien Anda</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-borderless align-middle datatable-init" id="tablePasienSaya">
            <thead>
                <tr style="border-bottom: 2px solid #f0f0f0;">
                    <th class="text-muted fw-bold pb-3">No</th>
                    <th class="text-muted fw-bold pb-3">Nama Pasien</th>
                    <th class="text-muted fw-bold pb-3">Usia / JK</th>
                    <th class="text-muted fw-bold pb-3">BB (kg)</th>
                    <th class="text-muted fw-bold pb-3">Alamat</th>
                    <th class="text-muted fw-bold pb-3">Tanggal Periksa</th>
                    <th class="text-muted fw-bold pb-3">BPJS</th>
                    <th class="text-muted fw-bold pb-3">Gejala</th>
                    <th class="text-muted fw-bold pb-3">Catatan Pemeriksaan</th>
                    <th class="text-muted fw-bold pb-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while($row = mysqli_fetch_assoc($result_pasien)): 
                    // Hitung Usia
                    $usia = '-';
                    if (!empty($row['tanggal_lahir'])) {
                        $dob = new DateTime($row['tanggal_lahir']);
                        $now = new DateTime();
                        $usia = $now->diff($dob)->y . ' Thn';
                    }
                ?>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td class="py-3"><?= $no++ ?></td>
                    <td class="py-3">
                        <div class="fw-bold"><?= htmlspecialchars($row['nama']) ?></div>
                        <div class="small text-muted"><?= htmlspecialchars($row['telpon']) ?></div>
                    </td>
                    <td class="py-3">
                        <div><?= $usia ?></div>
                        <div class="small text-muted text-capitalize"><?= !empty($row['jenis_kelamin']) ? htmlspecialchars($row['jenis_kelamin']) : '-' ?></div>
                    </td>
                    <td class="py-3"><?= !empty($row['berat_badan']) ? htmlspecialchars($row['berat_badan']) : '-' ?></td>
                    <td class="py-3"><div class="text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($row['alamat']) ?>"><?= !empty($row['alamat']) ? htmlspecialchars($row['alamat']) : '-' ?></div></td>
                    <td class="py-3 text-muted"><?= date('d M Y, H:i', strtotime($row['tanggal_janji'])) ?></td>
                    <td class="py-3">
                        <?php if($row['bpjs'] == 'ya'): ?>
                            <span class="badge bg-success rounded-pill px-3 py-2 fw-semibold" style="font-size: 11px;">Ya</span>
                        <?php else: ?>
                            <span class="badge bg-secondary rounded-pill px-3 py-2 fw-semibold" style="font-size: 11px;">Tidak</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3"><div class="text-truncate text-muted" style="max-width: 150px;" title="<?= htmlspecialchars($row['gejala']) ?>"><?= htmlspecialchars($row['gejala']) ?></div></td>
                    <td class="py-3">
                        <div class="text-truncate text-muted" style="max-width: 150px;" title="<?= htmlspecialchars($row['catatan']) ?>">
                            <?= !empty($row['catatan']) ? htmlspecialchars($row['catatan']) : '<i class="text-muted small">Belum ada</i>' ?>
                        </div>
                    </td>
                    <td class="py-3">
                        <button class="btn btn-sm btn-primary-custom rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalCatatan<?= $row['id_janji'] ?>" title="Edit Catatan & Status">
                            Edit
                        </button>
                    </td>
                </tr>

                <!-- Modal Edit Catatan & Status -->
                <div class="modal fade" id="modalCatatan<?= $row['id_janji'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <form action="../../config/function_janji_temu.php" method="POST">
                            <input type="hidden" name="btn_update_janji_temu" value="1">
                            <input type="hidden" name="id_janji_temu" value="<?= $row['id_janji'] ?>">
                            <div class="modal-content">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold">Update Pemeriksaan: <?= htmlspecialchars($row['nama']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Status Pemeriksaan</label>
                                        <select class="form-select" name="status">
                                            <option value="menunggu" <?= $row['status'] == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                            <option value="dikonfirmasi" <?= $row['status'] == 'dikonfirmasi' ? 'selected' : '' ?>>Dikonfirmasi</option>
                                            <option value="selesai" <?= $row['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                            <option value="dibatalkan" <?= $row['status'] == 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Catatan Pemeriksaan</label>
                                        <textarea class="form-control" name="catatan" rows="4" placeholder="Tuliskan hasil diagnosis, resep obat, atau anjuran untuk pasien ini..."><?= htmlspecialchars($row['catatan']) ?></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary-custom px-4">Simpan Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    if (!$.fn.DataTable.isDataTable('#tablePasienSaya')) {
        $('#tablePasienSaya').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' }
        });
    }
});
</script>

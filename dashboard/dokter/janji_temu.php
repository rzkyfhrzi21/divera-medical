<?php
// Ambil data janji temu untuk dokter ini
$user_id = $_SESSION['user_id'];
$q_dokter = mysqli_query($koneksi, "SELECT id FROM dokter WHERE id_pengguna='$user_id'");
$d_dokter = mysqli_fetch_assoc($q_dokter);
$id_dokter = $d_dokter ? $d_dokter['id'] : 0;

$q_janji = mysqli_query($koneksi, "
    SELECT j.*, pg.nama as nama_pasien 
    FROM janji_temu j 
    JOIN pasien ps ON j.id_pasien = ps.id 
    JOIN pengguna pg ON ps.id_pengguna = pg.id 
    WHERE j.id_dokter='$id_dokter' 
    ORDER BY j.tanggal_janji DESC
");
?>
<div class="content-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h5 class="fw-bold mb-0">Kelola Janji Temu</h5>
    </div>

    <div class="table-responsive">
        <table id="tabelJanjiTemu" class="table table-borderless align-middle" style="font-size: 14px; width:100%">
            <thead>
                <tr style="border-bottom: 2px solid #f0f0f0;">
                    <th width="5%" class="text-muted fw-bold pb-3">No</th>
                    <th class="text-muted fw-bold pb-3">Pasien</th>
                    <th class="text-muted fw-bold pb-3">Tanggal & Waktu</th>
                    <th class="text-muted fw-bold pb-3">Gejala</th>
                    <th class="text-muted fw-bold pb-3">Catatan Dokter</th>
                    <th class="text-muted fw-bold pb-3">Status</th>
                    <th width="15%" class="text-center text-muted fw-bold pb-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($q_janji)): ?>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td class="py-3"><?= $no++ ?></td>
                    <td class="fw-bold py-3"><?= htmlspecialchars($row['nama_pasien']) ?></td>
                    <td class="py-3"><?= date('d M Y, H:i', strtotime($row['tanggal_janji'])) ?></td>
                    <td class="py-3"><?= htmlspecialchars($row['gejala'] ?? '-') ?></td>
                    <td class="py-3"><?= htmlspecialchars($row['catatan'] ?? '-') ?></td>
                    <td class="py-3">
                        <?php
                        $badge = 'bg-secondary';
                        if ($row['status'] == 'menunggu') $badge = 'bg-warning text-dark';
                        elseif ($row['status'] == 'dikonfirmasi') $badge = 'bg-info text-dark';
                        elseif ($row['status'] == 'selesai') $badge = 'bg-success';
                        elseif ($row['status'] == 'dibatalkan') $badge = 'bg-danger';
                        ?>
                        <span class="badge <?= $badge ?>"><?= ucfirst($row['status']) ?></span>
                    </td>
                    <td class="text-center py-3">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit" 
                            data-bs-toggle="modal" data-bs-target="#editModal"
                            data-id="<?= $row['id'] ?>"
                            data-pasien="<?= htmlspecialchars($row['nama_pasien']) ?>"
                            data-tanggal="<?= $row['tanggal_janji'] ?>"
                            data-gejala="<?= htmlspecialchars($row['gejala']) ?>"
                            data-status="<?= $row['status'] ?>"
                            data-catatan="<?= htmlspecialchars($row['catatan']) ?>"
                            title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <form action="../../config/function_janji_temu.php" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin membatalkan/menghapus janji temu ini?');">
                            <input type="hidden" name="id_janji_temu" value="<?= $row['id'] ?>">
                            <button type="submit" name="btn_delete_janji_temu" class="btn btn-sm btn-outline-danger" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit Janji Temu (Large) -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="editModalLabel">Update Janji Temu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../config/function_janji_temu.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_janji_temu" id="edit_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Nama Pasien</label>
                            <input type="text" class="form-control bg-light" id="edit_pasien" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Tanggal Janji</label>
                            <input type="text" class="form-control bg-light" id="edit_tanggal" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Gejala (Dilaporkan Pasien)</label>
                        <textarea class="form-control bg-light" id="edit_gejala" rows="2" readonly></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Update Status <span class="text-danger">*</span></label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="menunggu">Menunggu Konfirmasi</option>
                            <option value="dikonfirmasi">Dikonfirmasi</option>
                            <option value="selesai">Selesai</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan Dokter</label>
                        <textarea name="catatan" id="edit_catatan" class="form-control" rows="3" placeholder="Tuliskan resep, diagnosa, atau pesan untuk pasien..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" name="btn_update_janji_temu" class="btn btn-primary-custom">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi DataTable
    $('#tabelJanjiTemu').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
        }
    });

    // Handle klik tombol edit
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('edit_id').value = this.getAttribute('data-id');
            document.getElementById('edit_pasien').value = this.getAttribute('data-pasien');
            document.getElementById('edit_tanggal').value = this.getAttribute('data-tanggal');
            document.getElementById('edit_gejala').value = this.getAttribute('data-gejala');
            document.getElementById('edit_status').value = this.getAttribute('data-status');
            document.getElementById('edit_catatan').value = this.getAttribute('data-catatan');
        });
    });
});
</script>

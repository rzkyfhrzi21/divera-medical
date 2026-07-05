<?php
// Ambil data pasien
$user_id = $_SESSION['user_id'];
$q_pasien = mysqli_query($koneksi, "SELECT id FROM pasien WHERE id_pengguna='$user_id'");
$d_pasien = mysqli_fetch_assoc($q_pasien);
$id_pasien = $d_pasien ? $d_pasien['id'] : 0;

$q_janji = mysqli_query($koneksi, "
    SELECT j.*, pg.nama as nama_dokter, d.spesialisasi
    FROM janji_temu j 
    JOIN dokter d ON j.id_dokter = d.id 
    JOIN pengguna pg ON d.id_pengguna = pg.id 
    WHERE j.id_pasien='$id_pasien' 
    ORDER BY j.tanggal_janji DESC
");
?>
<div class="content-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h5 class="fw-bold mb-0">Riwayat Janji Temu</h5>
        <a href="../../tanya-dokter" class="btn btn-primary-custom btn-sm"><i class="fa-solid fa-plus"></i> Buat Janji Baru</a>
    </div>

    <div class="table-responsive">
        <table id="tabelJanjiPasien" class="table table-borderless align-middle" style="font-size: 14px; width:100%">
            <thead>
                <tr style="border-bottom: 2px solid #f0f0f0;">
                    <th width="5%" class="text-muted fw-bold pb-3">No</th>
                    <th class="text-muted fw-bold pb-3">Dokter</th>
                    <th class="text-muted fw-bold pb-3">Spesialisasi</th>
                    <th class="text-muted fw-bold pb-3">Tanggal & Waktu</th>
                    <th class="text-muted fw-bold pb-3">Keluhan</th>
                    <th class="text-muted fw-bold pb-3">Catatan Dokter</th>
                    <th class="text-muted fw-bold pb-3">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($q_janji)): ?>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td class="py-3"><?= $no++ ?></td>
                    <td class="fw-bold py-3"><?= htmlspecialchars($row['nama_dokter']) ?></td>
                    <td class="py-3"><?= htmlspecialchars($row['spesialisasi']) ?></td>
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
                        <span class="badge <?= $badge ?> rounded-pill px-3 py-2 fw-semibold" style="font-size: 11px;"><?= ucfirst($row['status']) ?></span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi DataTable
    $('#tabelJanjiPasien').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
        }
    });
});
</script>

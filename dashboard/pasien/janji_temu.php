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
        <table id="tabelJanjiPasien" class="table table-bordered table-hover align-middle" style="font-size: 14px; width:100%">
            <thead class="table-light">
                <tr>
                    <th width="5%">No</th>
                    <th>Dokter</th>
                    <th>Spesialisasi</th>
                    <th>Tanggal & Waktu</th>
                    <th>Keluhan</th>
                    <th>Catatan Dokter</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($q_janji)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="fw-bold"><?= htmlspecialchars($row['nama_dokter']) ?></td>
                    <td><?= htmlspecialchars($row['spesialisasi']) ?></td>
                    <td><?= date('d M Y, H:i', strtotime($row['tanggal_janji'])) ?></td>
                    <td><?= htmlspecialchars($row['gejala'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['catatan'] ?? '-') ?></td>
                    <td>
                        <?php
                        $badge = 'bg-secondary';
                        if ($row['status'] == 'menunggu') $badge = 'bg-warning text-dark';
                        elseif ($row['status'] == 'dikonfirmasi') $badge = 'bg-info text-dark';
                        elseif ($row['status'] == 'selesai') $badge = 'bg-success';
                        elseif ($row['status'] == 'dibatalkan') $badge = 'bg-danger';
                        ?>
                        <span class="badge <?= $badge ?>"><?= ucfirst($row['status']) ?></span>
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

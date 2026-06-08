<?php
// Ambil data profil dokter dari database
$user_id = $_SESSION['user_id'];
$user_nama = $_SESSION['user_nama'];

// Cek apakah sudah punya data di tabel dokter
$query_dokter = mysqli_query($koneksi, "SELECT d.*, p.email, p.telpon FROM dokter d JOIN pengguna p ON d.id_pengguna = p.id WHERE d.id_pengguna = '$user_id'");
$data_dokter = mysqli_fetch_assoc($query_dokter);

// Hitung jumlah janji temu jika data dokter ada
$total_janji = 0;
$total_menunggu = 0;
$total_selesai = 0;
$janji_list = [];
if ($data_dokter) {
    $id_dokter = $data_dokter['id'];
    // Total semua janji temu
    $q = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM janji_temu WHERE id_dokter='$id_dokter'");
    $total_janji = mysqli_fetch_assoc($q)['total'];
    // Total menunggu
    $q = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM janji_temu WHERE id_dokter='$id_dokter' AND status='menunggu'");
    $total_menunggu = mysqli_fetch_assoc($q)['total'];
    // Total selesai
    $q = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM janji_temu WHERE id_dokter='$id_dokter' AND status='selesai'");
    $total_selesai = mysqli_fetch_assoc($q)['total'];
    // 5 janji temu terbaru
    $q = mysqli_query($koneksi, "
        SELECT j.*, pg.nama as nama_pasien 
        FROM janji_temu j 
        JOIN pasien ps ON j.id_pasien = ps.id 
        JOIN pengguna pg ON ps.id_pengguna = pg.id 
        WHERE j.id_dokter='$id_dokter' 
        ORDER BY j.tanggal_janji DESC LIMIT 5
    ");
    while ($row = mysqli_fetch_assoc($q)) {
        $janji_list[] = $row;
    }
}
?>
<!-- Greeting -->
<div class="content-card mb-4">
    <h5 class="fw-bold mb-1">Selamat datang, <?= htmlspecialchars($user_nama) ?>! 👋</h5>
    <p class="text-muted mb-0 small">Berikut ringkasan aktivitas praktik Anda hari ini.</p>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #E91E63, #F06292);">
            <p>Total Janji Temu</p>
            <h3><?= $total_janji ?></h3>
            <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #FF9800, #FFB74D);">
            <p>Menunggu Konfirmasi</p>
            <h3><?= $total_menunggu ?></h3>
            <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #4CAF50, #81C784);">
            <p>Selesai</p>
            <h3><?= $total_selesai ?></h3>
            <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
        </div>
    </div>
</div>

<!-- Janji Temu Terbaru -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
        <h6 class="fw-bold mb-0"><i class="fa-solid fa-list-check me-2 text-primary-custom"></i>Janji Temu Terbaru</h6>
        <a href="index?page=Janji Temu" class="text-primary-custom text-decoration-none small fw-bold">Lihat Semua →</a>
    </div>

    <?php if (count($janji_list) > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle" style="font-size: 13px;">
            <thead class="table-light">
                <tr>
                    <th>Pasien</th>
                    <th>Tanggal</th>
                    <th>Gejala</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($janji_list as $j): ?>
                <tr>
                    <td class="fw-bold"><?= htmlspecialchars($j['nama_pasien']) ?></td>
                    <td><?= date('d M Y, H:i', strtotime($j['tanggal_janji'])) ?></td>
                    <td class="text-truncate" style="max-width: 200px;"><?= htmlspecialchars($j['gejala'] ?? '-') ?></td>
                    <td>
                        <?php
                        $badge = 'bg-secondary';
                        if ($j['status'] == 'menunggu') $badge = 'bg-warning text-dark';
                        elseif ($j['status'] == 'dikonfirmasi') $badge = 'bg-info text-dark';
                        elseif ($j['status'] == 'selesai') $badge = 'bg-success';
                        elseif ($j['status'] == 'dibatalkan') $badge = 'bg-danger';
                        ?>
                        <span class="badge badge-status <?= $badge ?>"><?= ucfirst($j['status']) ?></span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Detail"><i class="fa-solid fa-eye"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
        <i class="fa-solid fa-calendar-xmark fa-3x text-muted mb-3"></i>
        <p class="text-muted">Belum ada janji temu. Data akan muncul saat pasien membuat reservasi.</p>
    </div>
    <?php endif; ?>
</div>

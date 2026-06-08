<?php
// Ambil data profil dari session & database
$user_id = $_SESSION['user_id'];
$user_nama = $_SESSION['user_nama'];

// Ambil data profil pasien (jika sudah ada di tabel pasien)
$query_pasien = mysqli_query($koneksi, "SELECT ps.*, pg.email, pg.telpon FROM pasien ps JOIN pengguna pg ON ps.id_pengguna = pg.id WHERE ps.id_pengguna = '$user_id'");
$data_pasien = mysqli_fetch_assoc($query_pasien);

// Ambil data pengguna untuk email & telpon meskipun belum punya profil pasien
$query_user = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE id = '$user_id'");
$data_user = mysqli_fetch_assoc($query_user);

// Hitung janji temu pasien
$total_janji = 0;
$total_menunggu = 0;
$janji_list = [];
if ($data_pasien) {
    $id_pasien = $data_pasien['id'];
    // Total janji
    $q = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM janji_temu WHERE id_pasien='$id_pasien'");
    $total_janji = mysqli_fetch_assoc($q)['total'];
    // Total menunggu
    $q = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM janji_temu WHERE id_pasien='$id_pasien' AND status='menunggu'");
    $total_menunggu = mysqli_fetch_assoc($q)['total'];
    // 5 janji temu terbaru
    $q = mysqli_query($koneksi, "
        SELECT j.*, pg.nama as nama_dokter, d.spesialisasi
        FROM janji_temu j 
        JOIN dokter d ON j.id_dokter = d.id 
        JOIN pengguna pg ON d.id_pengguna = pg.id 
        WHERE j.id_pasien='$id_pasien' 
        ORDER BY j.tanggal_janji DESC LIMIT 5
    ");
    while ($row = mysqli_fetch_assoc($q)) {
        $janji_list[] = $row;
    }
}
?>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="../../tanya-dokter" class="quick-action">
            <div class="qa-icon"><i class="fa-solid fa-stethoscope"></i></div>
            <div class="fw-bold small">Konsultasi Dokter</div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="../../obat-vitamin" class="quick-action">
            <div class="qa-icon"><i class="fa-solid fa-capsules"></i></div>
            <div class="fw-bold small">Beli Obat</div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="../../kesehatan" class="quick-action">
            <div class="qa-icon"><i class="fa-solid fa-newspaper"></i></div>
            <div class="fw-bold small">Artikel Kesehatan</div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="../../homecare" class="quick-action">
            <div class="qa-icon"><i class="fa-solid fa-house-medical"></i></div>
            <div class="fw-bold small">Homecare</div>
        </a>
    </div>
</div>

<!-- Info Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="content-card h-100">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-id-card me-2 text-primary-custom"></i>Info Akun</h6>
            <div class="mb-2"><span class="text-muted small">Nama:</span> <span class="fw-bold"><?= htmlspecialchars($user_nama) ?></span></div>
            <div class="mb-2"><span class="text-muted small">Email:</span> <span class="fw-bold"><?= htmlspecialchars($data_user['email'] ?? '-') ?></span></div>
            <div class="mb-2"><span class="text-muted small">Telepon:</span> <span class="fw-bold"><?= htmlspecialchars($data_user['telpon'] ?? '-') ?></span></div>
            <div><span class="text-muted small">Terdaftar:</span> <span class="fw-bold"><?= date('d M Y', strtotime($data_user['tgl_daftar'] ?? 'now')) ?></span></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="content-card h-100 text-center d-flex flex-column justify-content-center">
            <div class="text-primary-custom mb-2"><i class="fa-solid fa-calendar-check fa-2x"></i></div>
            <h3 class="fw-bold mb-0"><?= $total_janji ?></h3>
            <p class="text-muted small mb-0">Total Janji Temu</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="content-card h-100 text-center d-flex flex-column justify-content-center">
            <div class="text-warning mb-2"><i class="fa-solid fa-hourglass-half fa-2x"></i></div>
            <h3 class="fw-bold mb-0"><?= $total_menunggu ?></h3>
            <p class="text-muted small mb-0">Menunggu</p>
        </div>
    </div>
</div>

<!-- Janji Temu Terbaru -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0"><i class="fa-solid fa-list-check me-2 text-primary-custom"></i>Janji Temu Saya</h6>
        <a href="../../tanya-dokter" class="btn btn-primary-custom btn-sm rounded-pill px-3">
            <i class="fa-solid fa-plus me-1"></i> Buat Janji Baru
        </a>
    </div>

    <?php if (count($janji_list) > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle" style="font-size: 13px;">
            <thead class="table-light">
                <tr>
                    <th>Dokter</th>
                    <th>Spesialisasi</th>
                    <th>Tanggal</th>
                    <th>Gejala</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($janji_list as $j): ?>
                <tr>
                    <td class="fw-bold"><?= htmlspecialchars($j['nama_dokter']) ?></td>
                    <td class="text-muted"><?= htmlspecialchars($j['spesialisasi'] ?? '-') ?></td>
                    <td><?= date('d M Y, H:i', strtotime($j['tanggal_janji'])) ?></td>
                    <td class="text-truncate" style="max-width: 180px;"><?= htmlspecialchars($j['gejala'] ?? '-') ?></td>
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
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
        <i class="fa-solid fa-calendar-xmark fa-3x text-muted mb-3 d-block"></i>
        <p class="text-muted mb-2">Belum ada janji temu.</p>
        <a href="../../tanya-dokter" class="btn btn-primary-custom btn-sm rounded-pill px-4">Buat Janji Sekarang</a>
    </div>
    <?php endif; ?>
</div>

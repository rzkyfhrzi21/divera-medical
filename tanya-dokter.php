<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once 'config/koneksi.php';
$is_logged_in = isset($_SESSION['user_id']);
$user_nama = $is_logged_in ? $_SESSION['user_nama'] : 'Login';
$initial = strtoupper(substr($user_nama, 0, 1));
$dashboard_url = 'login';
if ($is_logged_in) {
    if ($_SESSION['user_role'] == 'admin') {
        $dashboard_url = 'dashboard/admin-dashboard.php';
    } else if ($_SESSION['user_role'] == 'dokter') {
        $dashboard_url = 'dashboard/dokter/';
    } else {
        $dashboard_url = 'index';
    }
}

$q_dokter = mysqli_query($koneksi, "
    SELECT d.id, d.spesialisasi, d.biografi, d.tahun_pengalaman, d.biaya, p.nama, p.foto_profil
    FROM dokter d
    JOIN pengguna p ON d.id_pengguna = p.id
    WHERE p.role = 'dokter'
    ORDER BY p.nama ASC
");
$dokters = [];
if ($q_dokter) {
    while ($row = mysqli_fetch_assoc($q_dokter)) {
        $dokters[] = $row;
    }
}
?>
<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tanya Dokter - DiVera Medical</title>
  <link rel="icon" href="asset/img/logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="asset/css/globals.css">
  <link rel="stylesheet" href="asset/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
      <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light shadow-sm sticky-top" style="background-color: #FAFAFA;">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center text-primary-custom fw-bold" href="index">
        <img src="asset/img/logo.png" alt="Logo" height="40" class="me-2"> DiVera Medical
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-auto align-items-center gap-lg-4 gap-2 py-3 py-lg-0">
          <li class="nav-item"><a class="nav-link text-dark" style="font-size: 13px;" href="index">Beranda</a></li>
          <li class="nav-item"><a class="nav-link text-dark" style="font-size: 13px;" href="homecare">Homecare</a></li>
          <li class="nav-item"><a class="nav-link text-primary-custom fw-bold" style="font-size: 13px;" href="tanya-dokter">Konsultasi</a></li>
          <li class="nav-item"><a class="nav-link text-dark" style="font-size: 13px;" href="obat-vitamin">Beli Obat</a></li>
          <li class="nav-item"><a class="nav-link text-dark" style="font-size: 13px;" href="kesehatan">Artikel</a></li>
          <li class="nav-item"><a class="nav-link text-dark" style="font-size: 13px;" href="kalender-kehamilan">Kehamilan</a></li>
          <li class="nav-item"><a class="nav-link text-dark" style="font-size: 13px;" href="tentang">Tentang</a></li>
        </ul>
        <a href="<?= $is_logged_in ? 'keranjang' : 'login' ?>" class="text-dark position-relative me-3 mt-2 mt-lg-0" title="Keranjang">
            <i class="fa-solid fa-cart-shopping fs-5"></i>
        </a>
        <?php if ($is_logged_in): ?>
        <div class="dropdown mt-2 mt-lg-0">
          <div class="d-flex align-items-center bg-white border rounded-pill px-3 py-1 shadow-sm" data-bs-toggle="dropdown" style="cursor: pointer;">
            <?php if(isset($_SESSION['user_foto']) && !empty($_SESSION['user_foto'])): ?>
            <img src="asset/img/profil/<?= htmlspecialchars($_SESSION['user_foto']) ?>" class="rounded-circle me-2" style="width: 25px; height: 25px; object-fit: cover;" alt="Profile">
            <?php else: ?>
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" style="width: 25px; height: 25px; background-color: #FFE6F0; color: #E91E63; font-size: 11px;"><?= $initial ?></div>
            <?php endif; ?>
            <span class="fw-bold text-dark" style="font-size: 13px;"><?= htmlspecialchars($user_nama) ?></span>
          </div>
          <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius: 12px; font-size: 14px;">
            <li><a class="dropdown-item py-2" href="#"><i class="fa-solid fa-user me-2 text-muted"></i> Profile</a></li>
            <li><a class="dropdown-item py-2" href="#"><i class="fa-solid fa-gear me-2 text-muted"></i> Setting</a></li>
            <li><a class="dropdown-item py-2" href="<?= $dashboard_url ?>"><i class="fa-solid fa-chart-line me-2 text-muted"></i> Dashboard</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item py-2 text-danger fw-bold" href="config/function_auth.php?action=logout"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
          </ul>
        </div>
<?php else: ?>
        <a href="login.php" class="text-decoration-none d-flex align-items-center bg-white border rounded-pill px-3 py-1 shadow-sm mt-2 mt-lg-0">
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" style="width: 25px; height: 25px; background-color: #FFE6F0; color: #E91E63; font-size: 11px;">L</div>
            <span class="fw-bold text-dark" style="font-size: 13px;">Login</span>
        </a>
<?php endif; ?>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="container py-5">
    <div class="row align-items-center mb-5">
      <div class="col-md-6">
        <h1 class="display-4 fw-bold text-primary-custom">Konsultasi Dokter Online</h1>
        <p class="lead">Dapatkan saran medis dari dokter terpercaya kami kapan saja, di mana saja tanpa harus keluar rumah.</p>
        <div class="input-group mt-4 shadow-sm">
          <input type="text" class="form-control form-control-lg" placeholder="Cari dokter spesialis atau keluhan...">
          <button class="btn btn-primary-custom px-4" type="button">Cari</button>
        </div>
      </div>
      <div class="col-md-6 text-center mt-4 mt-md-0">
        <img src="asset/img/female-doctor.png" alt="Dokter" class="img-fluid rounded-circle shadow-lg" style="max-height: 400px; object-fit: cover;">
      </div>
    </div>

    <h2 class="mb-4 text-center fw-bold">Dokter Pilihan Kami</h2>
    <div class="row g-4">
      <?php if (count($dokters) > 0): ?>
        <?php foreach ($dokters as $dokter): ?>
          <?php
            $dokter_id = (int) $dokter['id'];
            $nama_dokter = htmlspecialchars($dokter['nama']);
            $spesialisasi = !empty($dokter['spesialisasi']) ? htmlspecialchars($dokter['spesialisasi']) : 'Dokter Umum';
            $biaya = (float) ($dokter['biaya'] ?? 0);
            $pengalaman = (int) ($dokter['tahun_pengalaman'] ?? 0);
            $foto = !empty($dokter['foto_profil']) ? 'profil/' . $dokter['foto_profil'] : 'female-doctor.png';
          ?>
          <div class="col-md-4">
            <div class="card doctor-card h-100 p-3 text-center d-flex flex-column shadow-sm border-0 rounded-4">
              <img src="asset/img/<?= $foto ?>" class="rounded-circle mx-auto mb-3 object-fit-cover shadow-sm" alt="<?= $nama_dokter ?>" style="width: 100px; height: 100px;">
              <h5 class="card-title fw-bold"><?= $nama_dokter ?></h5>
              <p class="text-muted mb-1"><?= $spesialisasi ?></p>
              <div class="d-flex justify-content-center text-warning mb-2">
                ★★★★★ <span class="text-muted ms-2">(Baru)</span>
              </div>
              <p class="text-muted small mb-2"><?= $pengalaman > 0 ? $pengalaman . ' tahun pengalaman' : 'Pengalaman belum diatur' ?></p>
              <p class="text-success fw-bold mt-auto mb-3">Rp <?= number_format($biaya, 0, ',', '.') ?></p>
              <button
                type="button"
                class="btn btn-primary-custom w-100 rounded-pill"
                data-bs-toggle="modal"
                data-bs-target="#modalJanjiTemu<?= $dokter_id ?>">
                Buat Janji Temu
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="alert alert-light border text-center rounded-4 py-4">
            <h5 class="fw-bold mb-1">Belum ada dokter tersedia</h5>
            <p class="text-muted mb-0">Data dokter akan muncul setelah dokter melengkapi profilnya.</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <?php foreach ($dokters as $dokter): ?>
    <?php
      $dokter_id = (int) $dokter['id'];
      $nama_dokter = htmlspecialchars($dokter['nama']);
      $spesialisasi = !empty($dokter['spesialisasi']) ? htmlspecialchars($dokter['spesialisasi']) : 'Dokter Umum';
      $biografi = !empty($dokter['biografi']) ? htmlspecialchars($dokter['biografi']) : 'Dokter belum menambahkan biografi.';
      $biaya = (float) ($dokter['biaya'] ?? 0);
    ?>
    <div class="modal fade" id="modalJanjiTemu<?= $dokter_id ?>" tabindex="-1" aria-labelledby="modalJanjiTemuLabel<?= $dokter_id ?>" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="config/function_janji_temu.php" method="POST" class="modal-content border-0 rounded-4 shadow">
          <div class="modal-header border-0 pb-0">
            <div>
              <span class="badge rounded-pill text-bg-light text-primary-custom mb-2">Janji Temu Dokter</span>
              <h5 class="modal-title fw-bold" id="modalJanjiTemuLabel<?= $dokter_id ?>"><?= $nama_dokter ?></h5>
              <p class="text-muted small mb-0"><?= $spesialisasi ?></p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="btn_add_janji_temu" value="1">
            <input type="hidden" name="id_dokter" value="<?= $dokter_id ?>">

            <div class="p-3 rounded-4 mb-4" style="background-color:#fff1f7;">
              <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                <div>
                  <div class="fw-bold text-dark">Biaya konsultasi</div>
                  <div class="text-success fw-bold">Rp <?= number_format($biaya, 0, ',', '.') ?></div>
                </div>
                <div class="text-muted small"><?= nl2br($biografi) ?></div>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small fw-bold">Dokter</label>
                <input type="text" class="form-control" value="<?= $nama_dokter ?> - <?= $spesialisasi ?>" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold">Tanggal & Waktu Janji</label>
                <input type="datetime-local" name="tanggal_janji" class="form-control" required>
              </div>
              <div class="col-12">
                <label class="form-label small fw-bold">Gejala / Keluhan</label>
                <textarea name="gejala" class="form-control" rows="4" placeholder="Ceritakan keluhan utama, durasi gejala, dan informasi penting lainnya..." required></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary-custom rounded-pill px-4">
              Kirim Janji Temu
            </button>
          </div>
        </form>
      </div>
    </div>
  <?php endforeach; ?>

      <!-- Footer -->
  <footer class="pt-5 pb-4 mt-5" style="background-color: #FFF0F5;">
    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6">
          <a class="navbar-brand d-flex align-items-center text-primary-custom fw-bold mb-3" href="index">
            <img src="asset/img/logo.png" alt="Logo" height="40" class="me-2"> DiVera Medical
          </a>
          <p class="text-muted small">Kesehatan Anda adalah prioritas kami.</p>
        </div>
        <div class="col-lg-2 col-md-6">
          <h6 class="fw-bold text-primary-custom mb-3" style="font-size: 13px;">BANTUAN & PANDUAN</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#" class="text-muted text-decoration-none small">Pusat Bantuan</a></li>
            <li class="mb-2"><a href="#" class="text-muted text-decoration-none small">Syarat & Ketentuan</a></li>
            <li class="mb-2"><a href="#" class="text-muted text-decoration-none small">Kebijakan Privasi</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-6">
          <h6 class="fw-bold text-primary-custom mb-3" style="font-size: 13px;">LAYANAN</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="tanya-dokter" class="text-muted text-decoration-none small">Konsultasi Online</a></li>
            <li class="mb-2"><a href="#" class="text-muted text-decoration-none small">Atur Jadwal</a></li>
            <li class="mb-2"><a href="obat-vitamin" class="text-muted text-decoration-none small">Beli Obat</a></li>
            <li class="mb-2"><a href="#" class="text-muted text-decoration-none small">Riwayat Periksa</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-6">
          <h6 class="fw-bold text-primary-custom mb-3" style="font-size: 13px;">KLINIK</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="tentang" class="text-muted text-decoration-none small">Tentang Kami</a></li>
            <li class="mb-2"><a href="#" class="text-muted text-decoration-none small">Dokter Kami</a></li>
            <li class="mb-2"><a href="homecare" class="text-muted text-decoration-none small">Homecare</a></li>
            <li class="mb-2"><a href="#" class="text-muted text-decoration-none small">Kontak Klinik</a></li>
          </ul>
        </div>
      </div>
      <div class="mt-5">
        <p class="text-muted small mb-0">© 2026 DiVera Medical. Semua hak dilindungi.</p>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    <?php if (isset($_SESSION['flash'])): ?>
    Swal.fire({
        icon: '<?= $_SESSION['flash']['status'] === 'success' ? 'success' : 'error' ?>',
        title: '<?= $_SESSION['flash']['status'] === 'success' ? 'Berhasil' : 'Gagal' ?>',
        text: '<?= htmlspecialchars($_SESSION['flash']['message']) ?>',
        confirmButtonColor: '#E91E63'
    });
    <?php unset($_SESSION['flash']); endif; ?>
  </script>
</body>
</html>


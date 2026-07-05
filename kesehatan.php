<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
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
?>
<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kesehatan - DiVera Medical</title>
  <link rel="icon" href="asset/img/logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="asset/css/globals.css">
  <link rel="stylesheet" href="asset/css/style.css">
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
          <li class="nav-item"><a class="nav-link text-dark" style="font-size: 13px;" href="tanya-dokter">Konsultasi</a></li>
          <li class="nav-item"><a class="nav-link text-dark" style="font-size: 13px;" href="obat-vitamin">Beli Obat</a></li>
          <li class="nav-item"><a class="nav-link text-primary-custom fw-bold" style="font-size: 13px;" href="kesehatan">Artikel</a></li>
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
    <div class="hero-banner mb-5 text-center">
      <h1 class="display-5 fw-bold text-primary-custom mb-3">Pusat Informasi Kesehatan</h1>
      <p class="lead mb-4">Temukan artikel kesehatan terpercaya, tips gaya hidup sehat, dan informasi medis terkini dari para ahli DiVera Medical.</p>
      <div class="input-group mx-auto" style="max-width: 600px;">
        <input type="text" class="form-control" placeholder="Cari artikel kesehatan...">
        <button class="btn btn-primary-custom px-4" type="button">Cari</button>
      </div>
    </div>

    <h2 class="mb-4 fw-bold border-bottom pb-2">Artikel Terbaru</h2>
    <div class="row g-4 mb-5">
      <!-- Article 1 -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 article-card shadow-sm border-0">
          <img src="asset/img/600x400.jpg" class="card-img-top" alt="Artikel" style="height: 200px; object-fit: cover;">
          <div class="card-body d-flex flex-column">
            <span class="badge bg-primary-custom w-25 mb-2">Gaya Hidup</span>
            <h5 class="card-title fw-bold text-dark">Pentingnya Tidur Cukup Bagi Kekebalan Tubuh</h5>
            <p class="card-text text-muted">Tidur yang berkualitas memainkan peran penting dalam menjaga sistem imun agar tetap kuat melawan infeksi.</p>
            <a href="#" class="mt-auto text-primary-custom text-decoration-none fw-bold">Baca Selengkapnya &rarr;</a>
          </div>
        </div>
      </div>
      <!-- Article 2 -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 article-card shadow-sm border-0">
          <img src="asset/img/600x400.jpg" class="card-img-top" alt="Artikel" style="height: 200px; object-fit: cover;">
          <div class="card-body d-flex flex-column">
            <span class="badge bg-primary-custom w-25 mb-2">Jantung</span>
            <h5 class="card-title fw-bold text-dark">5 Kebiasaan Harian untuk Menjaga Jantung Sehat</h5>
            <p class="card-text text-muted">Hindari risiko penyakit kardiovaskular dengan menerapkan lima langkah sederhana ini setiap hari.</p>
            <a href="#" class="mt-auto text-primary-custom text-decoration-none fw-bold">Baca Selengkapnya &rarr;</a>
          </div>
        </div>
      </div>
      <!-- Article 3 -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 article-card shadow-sm border-0">
          <img src="asset/img/600x400.jpg" class="card-img-top" alt="Artikel" style="height: 200px; object-fit: cover;">
          <div class="card-body d-flex flex-column">
            <span class="badge bg-primary-custom w-25 mb-2">Mental</span>
            <h5 class="card-title fw-bold text-dark">Cara Mengelola Stres di Tempat Kerja</h5>
            <p class="card-text text-muted">Stres kronis dapat memicu berbagai penyakit fisik. Ketahui cara mengelolanya dengan bijak.</p>
            <a href="#" class="mt-auto text-primary-custom text-decoration-none fw-bold">Baca Selengkapnya &rarr;</a>
          </div>
        </div>
      </div>
    </div>
    
    <div class="text-center mt-4">
      <button class="btn btn-outline-primary-custom px-5 py-2 fw-bold">Muat Lebih Banyak Artikel</button>
    </div>
  </main>

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
    const themeToggle = document.getElementById('themeToggle');
    if(themeToggle) {
      themeToggle.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', newTheme);
        themeToggle.textContent = newTheme === 'dark' ? '☀️' : '🌓';
      });
    }
  </script>
</body>
</html>



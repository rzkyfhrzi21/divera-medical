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
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tentang Kami - DiVera Medical</title>
  <link rel="icon" href="asset/img/logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="asset/css/globals.css">
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
          <li class="nav-item"><a class="nav-link text-dark" style="font-size: 13px;" href="kesehatan">Artikel</a></li>
          <li class="nav-item"><a class="nav-link text-dark" style="font-size: 13px;" href="kalender-kehamilan">Kehamilan</a></li>
          <li class="nav-item"><a class="nav-link text-primary-custom fw-bold" style="font-size: 13px;" href="tentang">Tentang</a></li>
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

  <!-- Hero Section -->
  <section style="background-color: #E91E63; color: white; padding: 60px 0;">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-7">
          <div class="d-flex align-items-center mb-3">
            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
              <img src="asset/img/logo.png" alt="Logo" style="height: 22px;">
            </div>
            <h4 class="mb-0 fw-bold" style="color: #9DE6FA;"><span style="color: white;">Di</span>Vera</h4>
            <span class="ms-1 mt-1 fw-bold text-white" style="font-size: 14px;">Medical</span>
          </div>
          <h1 class="display-5 fw-bold mb-3">Tentang DiVera Medical</h1>
          <p class="fs-5" style="max-width: 600px;">Klinik digital modern yang mengutamakan pelayanan kesehatan cepat, ramah, dan terpercaya.</p>
        </div>
        <div class="col-lg-5 mt-4 mt-lg-0 text-center text-lg-end">
          <img src="asset/img/female-doctor.png" alt="Dokter" class="img-fluid rounded-4 shadow-sm" style="max-height: 250px; object-fit: cover;">
        </div>
      </div>
    </div>
  </section>

  <!-- Sejarah Klinik -->
  <section class="py-5" style="background-color: #FAFAFA;">
    <div class="container py-4">
      <h2 class="fw-bold text-primary-custom mb-4">Sejarah Klinik</h2>
      <p class="text-muted" style="line-height: 1.8; font-size: 15px;">
        DiVera Medical berdiri sebagai klinik kesehatan modern yang berfokus pada kemudahan akses layanan medis. Berawal dari kebutuhan masyarakat terhadap konsultasi kesehatan yang cepat dan nyaman, DiVera Medical mengembangkan layanan digital untuk konsultasi online, penjadwalan periksa, homecare, pembelian obat, dan edukasi kesehatan.
      </p>
      <p class="text-muted mt-3" style="line-height: 1.8; font-size: 15px;">
        Dengan dukungan tenaga medis profesional, DiVera Medical hadir untuk membantu pasien mendapatkan informasi, pemeriksaan, serta pendampingan kesehatan secara lebih terarah.
      </p>
    </div>
  </section>

  <!-- Misi DiVera Medical -->
  <section style="background-color: #79153D; color: white; padding: 70px 0;">
    <div class="container text-center py-3">
      <h2 class="fw-bold mb-4">Misi DiVera Medical</h2>
      <p class="mx-auto" style="max-width: 800px; font-size: 16px; line-height: 1.8;">
        Menyederhanakan akses layanan kesehatan melalui teknologi dan pelayanan klinik yang manusiawi, aman, dan terpercaya.
      </p>
    </div>
  </section>

  <!-- Layanan Cards -->
  <section class="py-5" style="background-color: #FAFAFA;">
    <div class="container" style="margin-top: -30px;">
      <div class="row g-4 justify-content-center">
        <div class="col-lg-3 col-md-6">
          <div class="bg-white rounded-4 p-4 shadow-sm h-100 border-0 text-center">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mt-2" style="width: 50px; height: 50px; background-color: #FFE6F0; color: #E91E63; font-size: 20px;">
              <i class="fa-regular fa-heart"></i>
            </div>
            <h6 class="fw-bold text-dark mb-2 mt-2">Konsultasi Online</h6>
            <p class="text-muted small mb-0 px-2">Layanan utama DiVera Medical untuk kebutuhan pasien.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="bg-white rounded-4 p-4 shadow-sm h-100 border-0 text-center">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mt-2" style="width: 50px; height: 50px; background-color: #FFE6F0; color: #E91E63; font-size: 20px;">
              <i class="fa-solid fa-plus"></i>
            </div>
            <h6 class="fw-bold text-dark mb-2 mt-2">Atur Jadwal Periksa</h6>
            <p class="text-muted small mb-0 px-2">Layanan utama DiVera Medical untuk kebutuhan pasien.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="bg-white rounded-4 p-4 shadow-sm h-100 border-0 text-center">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mt-2" style="width: 50px; height: 50px; background-color: #FFE6F0; color: #E91E63; font-size: 20px;">
              <i class="fa-regular fa-heart"></i>
            </div>
            <h6 class="fw-bold text-dark mb-2 mt-2">Beli Obat</h6>
            <p class="text-muted small mb-0 px-2">Layanan utama DiVera Medical untuk kebutuhan pasien.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="bg-white rounded-4 p-4 shadow-sm h-100 border-0 text-center">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mt-2" style="width: 50px; height: 50px; background-color: #FFE6F0; color: #E91E63; font-size: 20px;">
              <i class="fa-solid fa-plus"></i>
            </div>
            <h6 class="fw-bold text-dark mb-2 mt-2">Riwayat Periksa</h6>
            <p class="text-muted small mb-0 px-2">Layanan utama DiVera Medical untuk kebutuhan pasien.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

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
</body>
</html>


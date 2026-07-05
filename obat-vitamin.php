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
        $dashboard_url = 'dashboard/pasien/';
    }
}
?>
<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Obat & Vitamin - DiVera Medical</title>
  <link rel="icon" href="asset/img/logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
          <li class="nav-item"><a class="nav-link text-dark" style="font-size: 13px;" href="tanya-dokter">Konsultasi</a></li>
          <li class="nav-item"><a class="nav-link text-primary-custom fw-bold" style="font-size: 13px;" href="obat-vitamin">Beli Obat</a></li>
          <li class="nav-item"><a class="nav-link text-dark" style="font-size: 13px;" href="kesehatan">Artikel</a></li>
          <li class="nav-item"><a class="nav-link text-dark" style="font-size: 13px;" href="kalender-kehamilan">Kehamilan</a></li>
          <li class="nav-item"><a class="nav-link text-dark" style="font-size: 13px;" href="tentang">Tentang</a></li>
        </ul>
        <div class="d-flex align-items-center gap-3">
          <!-- Cart Icon -->
          <a href="<?= $is_logged_in ? 'keranjang' : 'login' ?>" class="text-dark position-relative text-decoration-none">
            <i class="fa-solid fa-cart-shopping fs-5"></i>
            <?php
            $cart_count = 0;
            if ($is_logged_in) {
                $uid = $_SESSION['user_id'];
                $q_cart = mysqli_query($koneksi, "SELECT SUM(kuantitas) as total FROM keranjang WHERE id_pengguna='$uid'");
                $d_cart = mysqli_fetch_assoc($q_cart);
                $cart_count = $d_cart['total'] ? $d_cart['total'] : 0;
            }
            ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary-custom" style="font-size: 9px;">
              <?= $cart_count ?>
            </span>
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
    </div>
  </nav>

  <!-- Main Content -->
  <main class="container py-5">
    <div class="text-center mb-5">
      <h1 class="display-5 fw-bold text-primary-custom">Apotek DiVera Medical</h1>
      <p class="lead">Beli obat dan vitamin asli dengan resep maupun tanpa resep, diantar langsung ke rumah Anda.</p>
    </div>

    <!-- Categories -->
    <div class="row text-center mb-5 g-3">
      <div class="col-6 col-md-3">
        <div class="p-3 border rounded text-dark category-btn shadow-sm" style="cursor:pointer; background-color: #fff; transition: 0.3s;" onmouseover="this.style.backgroundColor='#FDE7EF'; this.style.borderColor='#E91E63'" onmouseout="this.style.backgroundColor='#fff'; this.style.borderColor='#dee2e6'">💊 Vitamin & Suplemen</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="p-3 border rounded text-dark category-btn shadow-sm" style="cursor:pointer; background-color: #fff; transition: 0.3s;" onmouseover="this.style.backgroundColor='#FDE7EF'; this.style.borderColor='#E91E63'" onmouseout="this.style.backgroundColor='#fff'; this.style.borderColor='#dee2e6'">🤧 Obat Flu & Batuk</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="p-3 border rounded text-dark category-btn shadow-sm" style="cursor:pointer; background-color: #fff; transition: 0.3s;" onmouseover="this.style.backgroundColor='#FDE7EF'; this.style.borderColor='#E91E63'" onmouseout="this.style.backgroundColor='#fff'; this.style.borderColor='#dee2e6'">🤕 Pereda Nyeri</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="p-3 border rounded text-dark category-btn shadow-sm" style="cursor:pointer; background-color: #fff; transition: 0.3s;" onmouseover="this.style.backgroundColor='#FDE7EF'; this.style.borderColor='#E91E63'" onmouseout="this.style.backgroundColor='#fff'; this.style.borderColor='#dee2e6'">🍼 Ibu & Anak</div>
      </div>
    </div>

    <h3 class="mb-4 fw-bold">Produk Populer</h3>
    <div class="row g-4">
      <?php
      $query_produk = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY id ASC LIMIT 8");
      while ($p = mysqli_fetch_assoc($query_produk)):
      ?>
      <div class="col-md-3 col-sm-6">
        <div class="card product-card h-100 p-3 text-center border-0 shadow-sm">
          <img src="asset/img/<?= !empty($p['url_gambar']) && file_exists('asset/img/produk/'.$p['url_gambar']) ? 'produk/'.$p['url_gambar'] : '600x400.jpg' ?>" class="img-fluid rounded mb-3" alt="<?= htmlspecialchars($p['nama_produk']) ?>" style="height: 150px; object-fit: cover;">
          <h6 class="card-title fw-bold text-dark"><?= htmlspecialchars($p['nama_produk']) ?></h6>
          <p class="text-muted small mb-2"><?= htmlspecialchars($p['kategori']) ?></p>
          <p class="text-primary-custom fw-bold mb-3">Rp <?= number_format($p['harga'], 0, ',', '.') ?></p>
          
          <?php if ($p['stok'] > 0): ?>
          <form action="config/function_product.php" method="POST">
             <input type="hidden" name="id_produk" value="<?= $p['id'] ?>">
             <button type="submit" name="btn_add_keranjang" class="btn btn-outline-primary-custom w-100 rounded-pill">+ Keranjang</button>
          </form>
          <?php else: ?>
          <button class="btn btn-secondary w-100 rounded-pill disabled" style="opacity: 0.7;">Produk Habis</button>
          <?php endif; ?>
        </div>
      </div>
      <?php endwhile; ?>
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

    <?php 
    $flash = null;
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
    }
    if ($flash): ?>
    Swal.fire({
        icon: '<?= $flash['status'] === 'success' ? 'success' : 'error' ?>',
        title: '<?= $flash['status'] === 'success' ? 'Berhasil' : 'Gagal' ?>',
        text: '<?= htmlspecialchars($flash['message']) ?>',
        confirmButtonColor: '#E91E63'
    });
    <?php endif; ?>
  </script>
</body>
</html>

<?php 
session_start(); 

// Redirect otomatis jika sudah login
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'dokter') {
        header("Location: dashboard/dokter/");
        exit;
    } else if ($_SESSION['user_role'] === 'pasien') {
        header("Location: dashboard/pasien/");
        exit;
    }
}

// Ambil flash message dari session (jika ada), lalu hapus
$flash = null;
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar - DiVera Medical</title>
  <link rel="icon" href="asset/img/logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="asset/css/globals.css">
  <style>
    body, html { height: 100%; background-color: #FAFAFA; }
    .register-left {
      background-color: #FFE6F0;
      height: 100vh;
      display: flex;
      flex-direction: column;
      padding: 40px;
    }
    .register-right {
      height: 100vh;
      overflow-y: auto;
    }
  </style>
</head>
<body>
  <div class="container-fluid p-0">
    <div class="row m-0 w-100 h-100">
      <!-- Left Panel -->
      <div class="col-lg-4 d-none d-lg-flex register-left position-fixed top-0 start-0">
        <a href="index" class="d-flex align-items-center text-decoration-none mb-5">
          <img src="asset/img/logo.png" alt="Logo" height="40" class="me-2">
          <h4 class="m-0 fw-bold" style="color: #E91E63;">DiVera <br><span style="font-size: 14px; color: #4A1C2F;">Medical</span></h4>
        </a>
        <div class="mt-4">
          <h1 class="fw-bold mb-4" style="color: #111626; font-size: 32px;">Daftar Akun Pasien</h1>
          <p class="text-muted" style="font-size: 15px; line-height: 1.6; color: #606672 !important;">
            Buat akun untuk mengelola konsultasi online, jadwal periksa, riwayat pasien, dan layanan klinik DiVera Medical.
          </p>
        </div>
        <div class="mt-auto d-flex flex-column align-items-center">
          <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 70px; height: 70px; color: #E91E63; font-size: 28px;">
            <i class="fa-solid fa-plus"></i>
          </div>
          <h5 class="fw-bold text-primary-custom m-0">Doctor Registration</h5>
        </div>
      </div>

      <!-- Right Panel -->
      <div class="col-lg-8 offset-lg-4 register-right p-0">
        <!-- Top Nav -->
        <div class="d-flex justify-content-end align-items-center py-3 px-4 bg-white border-bottom">
          <div class="d-flex gap-4 me-4 d-none d-md-flex">
            <a href="#" class="text-muted text-decoration-none small">Jadwal</a>
            <a href="obat-vitamin" class="text-muted text-decoration-none small">Beli Obat</a>
            <a href="#" class="text-muted text-decoration-none small">Riwayat</a>
            <a href="#" class="text-muted text-decoration-none small">Bantuan</a>
            <a href="tentang" class="text-muted text-decoration-none small">Tentang</a>
          </div>
          <a href="login" class="btn btn-primary-custom btn-sm rounded-pill fw-bold px-3">
            <i class="fa-solid fa-right-to-bracket me-1"></i> Login
          </a>
        </div>

        <div class="d-flex align-items-center justify-content-center py-5" style="min-height: calc(100vh - 70px);">
          <div class="bg-white rounded-4 shadow-sm p-5 w-100" style="max-width: 550px; border: 1px solid #ebe6e9;">
            <h3 class="fw-bold text-dark mb-4">Buat Akun Baru</h3>
            <form action="config/function_auth.php" method="post">
              <input type="hidden" name="role" value="pasien">
              <div class="mb-3">
                <label class="form-label fw-bold small text-dark">Nama Lengkap</label>
                <input type="text" name="nama" required class="form-control form-control-lg" placeholder="Tata Difa Ananda" style="font-size: 14px; border-radius: 10px; border: 1px solid #ebe6e9;">
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold small text-dark">Email</label>
                <input type="email" name="email" required class="form-control form-control-lg" placeholder="tatadifaan.2411050073@mail.darmajaya.ac.id" style="font-size: 14px; border-radius: 10px; border: 1px solid #ebe6e9;">
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold small text-dark">Nomor Telepon</label>
                <input type="text" name="telepon" required class="form-control form-control-lg" placeholder="Masukkan nomor telepon" style="font-size: 14px; border-radius: 10px; border: 1px solid #ebe6e9;">
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold small text-dark">Password</label>
                <input type="password" name="password" required class="form-control form-control-lg" placeholder="Minimal 8 karakter" style="font-size: 14px; border-radius: 10px; border: 1px solid #ebe6e9;">
              </div>
              <div class="mb-4">
                <label class="form-label fw-bold small text-dark">Konfirmasi Password</label>
                <input type="password" name="password_confirm" required class="form-control form-control-lg" placeholder="Ulangi password" style="font-size: 14px; border-radius: 10px; border: 1px solid #ebe6e9;">
              </div>
              <button type="submit" name="btn_register" class="btn btn-primary-custom w-100 btn-lg fw-bold" style="border-radius: 10px; font-size: 15px;">Daftar Sekarang</button>
            </form>

            <!-- Tombol ke Login -->
            <div class="text-center mt-3">
              <span class="text-muted small">Sudah punya akun?</span>
              <a href="login" class="text-primary-custom fw-bold text-decoration-none small ms-1">Login di sini</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
<script>
<?php if ($flash && $flash['status'] === 'success'): ?>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '<?= htmlspecialchars($flash['message']) ?>',
    confirmButtonColor: '#E91E63',
    confirmButtonText: 'Ke Halaman Login',
    allowOutsideClick: false
}).then((result) => {
    if (result.isConfirmed) {
        window.location.href = 'login';
    }
});
<?php elseif ($flash && $flash['status'] === 'error'): ?>
Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: '<?= htmlspecialchars($flash['message']) ?>',
    confirmButtonColor: '#E91E63'
});
<?php endif; ?>
</script>
</html>

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

// Ambil flash message dari session (jika ada), lalu hapus agar tidak muncul lagi saat refresh
$flash = null;
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}
// Ambil redirect URL untuk login success
$redirect_url = null;
if (isset($_SESSION['redirect_url'])) {
    $redirect_url = $_SESSION['redirect_url'];
    unset($_SESSION['redirect_url']);
}
?>
<!doctype html>
<html lang="id" data-bs-theme="light">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - DiVera Medical</title>
    <link rel="icon" href="asset/img/logo.png" type="image/png" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="asset/css/globals.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
  <body>
    <div class="container-fluid p-0 login-container">
      <div class="row w-100 m-0">
        <!-- Left Side -->
        <div class="col-lg-6 d-none d-lg-flex login-left">
          <div>
            <div class="d-flex align-items-center mb-5">
              <div class="brand-logo me-3">+</div>
              <!-- <h2 class="fw-bold text-primary-custom m-0">DiVera Medical</h2> -->
            </div>
            <h1 class="display-4 fw-bold mb-3" style="color: #111626">
              Masuk ke akun dokter
            </h1>
            <p class="fs-4 fw-semibold mb-2" style="color: #111626">
              Kelola jadwal konsultasi, layanan pasien, dan aktivitas klinik
              dalam satu dashboard yang rapi.
            </p>
            <p class="text-muted">
              Pelayanan kesehatan digital yang mudah, nyaman, dan terpercaya
              untuk dokter serta pasien.
            </p>
          </div>
          <div class="text-center position-relative mt-5">
            <div
              class="position-absolute bg-white rounded-pill px-3 py-1 shadow-sm fw-bold text-primary-custom border border-dark"
              style="top: 20px; right: 15%; z-index: 10"
            >
              24/7 Care
            </div>
            <div class="bg-white rounded-4 p-3 d-inline-block shadow-sm">
              <img
                src="asset/img/female-doctor.png"
                alt="Dokter"
                class="img-fluid rounded-3"
                style="max-height: 280px; object-fit: cover"
              />
            </div>
          </div>
        </div>

        <!-- Right Side -->
        <div class="col-lg-6 login-right">
          <div class="position-absolute top-0 end-0 p-4 d-flex align-items-center gap-2">
            <a href="index" class="btn btn-outline-secondary btn-sm rounded-pill fw-bold"><i class="fa-solid fa-arrow-left me-1"></i> Beranda</a>
            <button
              class="btn btn-outline-secondary btn-sm rounded-circle"
              id="themeToggle"
              title="Toggle Dark Mode"
            >
              🌓
            </button>
          </div>
          <div class="login-card">
            <h2 class="fw-bold mb-1" style="color: #111626">Selamat Datang</h2>
            <p class="text-muted mb-4">Login DiVera Medical</p>

            <form action="config/function_auth.php" method="post">
              <div class="mb-3">
                <label class="form-label fw-bold" style="color: #111626"
                  >Email</label
                >
                <input
                  type="email"
                  name="email"
                  required
                  class="form-control form-control-lg bg-light border-0"
                  placeholder="Masukkan email"
                  style="
                    border-radius: 14px;
                    box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.02);
                  "
                />
              </div>
              <div class="mb-4">
                <label class="form-label fw-bold" style="color: #111626"
                  >Password</label
                >
                <input
                  type="password"
                  name="password"
                  required
                  class="form-control form-control-lg bg-light border-0"
                  placeholder="***"
                  style="
                    border-radius: 14px;
                    box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.02);
                  "
                />
              </div>

              <div class="d-flex justify-content-end mb-4">
                <a
                  href="#"
                  class="text-primary-custom fw-semibold text-decoration-none small"
                  >Lupa password?</a
                >
              </div>

              <button
                type="submit"
                name="btn_login"
                class="btn btn-primary-custom w-100 btn-lg mb-3 fw-bold"
                style="border-radius: 12px"
              >
                Masuk Dashboard
              </button>
              <a
                href="register"
                class="btn btn-outline-primary w-100 btn-lg fw-bold d-block"
                style="
                  border-color: var(--primary-color);
                  color: var(--primary-color);
                  border-radius: 12px;
                "
              >
                Daftar Akun Pasien
              </a>
            </form>
          </div>

          <div class="text-center mt-5">
            <p class="text-muted small mb-0">
              © 2026 DiVera Medical. Semua hak dilindungi.
            </p>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      const themeToggle = document.getElementById("themeToggle");
      themeToggle.addEventListener("click", () => {
        const currentTheme =
          document.documentElement.getAttribute("data-bs-theme");
        const newTheme = currentTheme === "dark" ? "light" : "dark";
        document.documentElement.setAttribute("data-bs-theme", newTheme);
        themeToggle.textContent = newTheme === "dark" ? "☀️" : "🌓";

        // Fix text colors for dark mode
        document.querySelectorAll("h1, h2, label, p.fs-4").forEach((el) => {
          if (newTheme === "dark") {
            el.style.color = "#ffffff";
          } else {
            el.style.color = "#111626";
          }
        });
      });

      <?php if ($flash && $flash['status'] === 'login_success'): ?>
      Swal.fire({
          icon: 'success',
          title: '<?= htmlspecialchars($flash['message']) ?>',
          text: 'Anda akan dialihkan ke Dashboard.',
          showCancelButton: false,
          confirmButtonColor: '#E91E63',
          confirmButtonText: 'Ke Dashboard',
          allowOutsideClick: false
      }).then((result) => {
          if (result.isConfirmed) {
              window.location.href = '<?= $redirect_url ?? "index" ?>';
          }
      });
      <?php elseif ($flash && $flash['status'] === 'error'): ?>
      Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: '<?= htmlspecialchars($flash['message']) ?>',
          confirmButtonColor: '#E91E63'
      });
      <?php elseif ($flash && $flash['status'] === 'success'): ?>
      Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: '<?= htmlspecialchars($flash['message']) ?>',
          confirmButtonColor: '#E91E63'
      });
      <?php endif; ?>
    </script>
  </body>
</html>

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
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - DiVera Medical</title>
    <link rel="icon" href="asset/img/logo.png" type="image/png" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="asset/css/globals.css" />
    <!-- Styles moved to globals.css -->
  </head>
  <body>
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="mb-4 d-flex align-items-center justify-content-between sidebar-header">
        <div class="sidebar-logo-container">
          <img
            src="asset/img/logo.png"
            alt="DiVera Medical"
            height="32"
          />
        </div>
        <button class="btn btn-light d-none d-lg-block" id="desktopMenuToggle">
          ☰
        </button>
      </div>

      <nav class="d-flex flex-column gap-1">
        <a href="index" class="nav-item-custom active">
          <i
            class="fa-solid fa-house-chimney"
            style="width: 16px; text-align: center"
          ></i>
          <span class="nav-text ms-2">Beranda</span>
        </a>
        <a href="homecare" class="nav-item-custom">
          <i
            class="fa-solid fa-hand-holding-medical"
            style="width: 16px; text-align: center"
          ></i>
          <span class="nav-text ms-2">Homecare</span>
        </a>
        <a href="tentang" class="nav-item-custom">
          <i
            class="fa-regular fa-building"
            style="width: 16px; text-align: center"
          ></i>
          <span class="nav-text ms-2">Tentang Kami</span>
        </a>
        <a href="kesehatan" class="nav-item-custom">
          <i
            class="fa-solid fa-notes-medical"
            style="width: 16px; text-align: center"
          ></i>
          <span class="nav-text ms-2">Kesehatan</span>
        </a>
        <a href="kalender-kehamilan" class="nav-item-custom">
          <i
            class="fa-solid fa-calendar-days"
            style="width: 16px; text-align: center"
          ></i>
          <span class="nav-text ms-2">Kalender Kehamilan</span>
        </a>
        <a href="login" class="nav-item-custom">
          <i
            class="fa-solid fa-gear"
            style="width: 16px; text-align: center"
          ></i>
          <span class="nav-text ms-2">Login</span>
        </a>
        <!-- Add Links to Other Figma Pages for easy access -->
        <a
          href="tanya-dokter"
          class="nav-item-custom mt-3 border-top pt-3"
        >
          <i
            class="fa-solid fa-user-doctor"
            style="width: 16px; text-align: center"
          ></i>
          <span class="nav-text ms-2">Tanya Dokter</span>
        </a>
        <a href="perawatan-kulit" class="nav-item-custom">
          <i
            class="fa-solid fa-spa"
            style="width: 16px; text-align: center"
          ></i>
          <span class="nav-text ms-2">Perawatan Kulit</span>
        </a>
        <a href="obat-vitamin" class="nav-item-custom">
          <i
            class="fa-solid fa-capsules"
            style="width: 16px; text-align: center"
          ></i>
          <span class="nav-text ms-2">Obat & Vitamin</span>
        </a>
        <a href="program-diet" class="nav-item-custom">
          <i
            class="fa-solid fa-apple-whole"
            style="width: 16px; text-align: center"
          ></i>
          <span class="nav-text ms-2">Program Diet</span>
        </a>
      </nav>

      <div class="promo-card">
        <div class="promo-icon"><i class="fa-solid fa-plus"></i></div>
        <h6
          class="fw-bold"
          style="color: #131724; font-size: 13px; line-height: 1.4"
        >
          Jaga Kesehatan,<br />Jaga Masa Depan
        </h6>
        <p class="small mb-4 mt-3" style="color: #606672; font-size: 10px">
          Periksa kesehatan rutin untuk<br />hidup yang lebih sehat dan bahagia.
        </p>
        <button
          class="btn btn-outline-primary-custom btn-sm rounded-pill w-100 fw-bold py-2"
          style="background-color: transparent"
        >
          Selengkapnya
        </button>
      </div>
      <div style="min-height: 32px; flex-shrink: 0; width: 100%;"></div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <!-- Mobile menu toggle button -->
        <button class="btn btn-light d-lg-none" id="mobileMenuToggle">
          ☰
        </button>


        <div class="search-box flex-grow-1 mx-lg-3 position-relative">
          <span class="text-muted">⌕</span>
          <input
            type="text"
            class="search-input"
            id="globalSearchInput"
            placeholder="Cari layanan, dokter, artikel kesehatan..."
            autocomplete="off"
          />
          <!-- AJAX Search Results Dropdown -->
          <div id="searchResults" class="position-absolute bg-white shadow-sm rounded-4 w-100 d-none" style="top: 100%; left: 0; z-index: 1050; margin-top: 10px; max-height: 300px; overflow-y: auto; border: 1px solid #ebe6e9;">
          </div>
        </div>

        <div class="d-flex align-items-center gap-3 topbar-actions">
          <a href="<?= $is_logged_in ? 'keranjang' : 'login' ?>" class="text-dark position-relative me-2" title="Keranjang">
            <i class="fa-solid fa-cart-shopping fs-5"></i>
          </a>
          <button
            class="btn btn-outline-secondary btn-sm rounded-circle"
            id="themeToggle"
            title="Toggle Dark Mode"
          >
            🌓
          </button>
          <div class="dropdown">
            <div
              class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm position-relative"
              style="width: 40px; height: 40px; cursor: pointer"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <span style="color: var(--primary-color)">🔔</span>
              <span
                class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"
              >
                <span class="visually-hidden">New alerts</span>
              </span>
            </div>
            <ul
              class="dropdown-menu dropdown-menu-end shadow border-0"
              style="width: 300px; max-height: 400px; overflow-y: auto"
            >
              <li>
                <h6 class="dropdown-header fw-bold text-dark fs-6">
                  Notifikasi
                </h6>
              </li>
              <li><hr class="dropdown-divider" /></li>
              <li>
                <a class="dropdown-item py-2" href="#">
                  <div class="d-flex align-items-center">
                    <div
                      class="bg-primary-custom text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                      style="width: 30px; height: 30px"
                    >
                      📅
                    </div>
                    <div>
                      <p class="mb-0 fw-bold" style="font-size: 13px">
                        Pengingat Jadwal
                      </p>
                      <p class="text-muted mb-0" style="font-size: 11px">
                        Konsultasi dengan dr. Amanda dalam 1 jam.
                      </p>
                    </div>
                  </div>
                </a>
              </li>
              <li>
                <a class="dropdown-item py-2" href="#">
                  <div class="d-flex align-items-center">
                    <div
                      class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                      style="width: 30px; height: 30px"
                    >
                      💊
                    </div>
                    <div>
                      <p class="mb-0 fw-bold" style="font-size: 13px">
                        Resep Siap Diambil
                      </p>
                      <p class="text-muted mb-0" style="font-size: 11px">
                        Obat dan vitamin Anda sudah tersedia di apotek.
                      </p>
                    </div>
                  </div>
                </a>
              </li>
              <li>
                <a class="dropdown-item py-2" href="#">
                  <div class="d-flex align-items-center">
                    <div
                      class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center me-3"
                      style="width: 30px; height: 30px"
                    >
                      📄
                    </div>
                    <div>
                      <p class="mb-0 fw-bold" style="font-size: 13px">
                        Hasil Lab Keluar
                      </p>
                      <p class="text-muted mb-0" style="font-size: 11px">
                        Hasil medical check-up Anda sudah bisa dilihat.
                      </p>
                    </div>
                  </div>
                </a>
              </li>
              <li><hr class="dropdown-divider" /></li>
              <li>
                <a
                  class="dropdown-item text-center text-primary-custom fw-bold"
                  href="#"
                  >Lihat Semua</a
                >
              </li>
            </ul>
          </div>
          <?php if ($is_logged_in): ?>
          <div class="dropdown">
            <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown" style="cursor: pointer;">
              <?php 
                $foto_profil = isset($_SESSION['user_foto']) && !empty($_SESSION['user_foto']) ? 'asset/img/profil/' . $_SESSION['user_foto'] : 'asset/img/icon-female.png';
              ?>
              <img src="<?= htmlspecialchars($foto_profil) ?>" alt="Profile" class="rounded-circle border" width="40" height="40" style="object-fit: cover; border-color: #EBE6E9 !important;" />
              <div class="d-none d-md-block">
                <div class="fw-bold text-dark" style="font-size: 14px"><?= htmlspecialchars($user_nama) ?></div>
                <div class="text-muted text-capitalize" style="font-size: 12px"><?= htmlspecialchars($_SESSION["user_role"] ?? "Pasien") ?></div>
              </div>
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
          <div>
            <a href="login.php" class="btn btn-primary-custom fw-bold rounded-pill px-4">Login</a>
          </div>
<?php endif; ?>
        </div>
      </header>

      <div class="row">
        <!-- Middle Column -->
        <div class="col-xl-9 col-lg-8">
          <!-- Hero Section -->
          <section
            class="hero-dashboard d-flex align-items-center justify-content-between"
          >
            <div>
              <h1
                class="display-5 fw-bolder mb-0 text-hero-custom"
                style="color: #4a1c2f"
              >
                Kesehatan Anda,
              </h1>
              <h1 class="display-5 fw-bolder text-primary-custom mb-3">
                Prioritas Kami
              </h1>
              <p
                class="text-hero-custom mb-4"
                style="max-width: 350px; color: #4a1c2f"
              >
                DiVera Medical hadir untuk memberikan pelayanan kesehatan
                modern, cepat, dan tepercaya untuk Anda dan keluarga.
              </p>
              <div class="d-flex gap-3">
                <button class="btn btn-primary-custom fw-bold rounded-3 px-4">
                  Buat Janji
                </button>
                <button
                  class="btn btn-outline-primary fw-bold rounded-3 px-4 bg-white"
                  style="
                    border-color: var(--primary-color);
                    color: var(--primary-color);
                  "
                >
                  Konsultasi Online
                </button>
              </div>
            </div>
            <div class="d-none d-md-block position-relative">
              <div
                class="position-absolute bg-white rounded-pill px-3 py-1 shadow-sm fw-bold text-primary-custom border border-dark"
                style="top: -15px; right: -10px; z-index: 10"
              >
                24/7 Care
              </div>
              <div class="bg-white rounded-4 p-2 shadow-sm">
                <img
                  src="asset/img/female-doctor.png"
                  alt="Dokter"
                  class="rounded-3"
                  style="width: 150px; height: 180px; object-fit: cover"
                />
              </div>
            </div>
          </section>

          <!-- Trust Cards -->
          <div class="row g-3 mb-4">
            <div class="col-sm-6 col-md-3">
              <div class="t-card">
                <div class="t-icon">
                  <i class="fa-solid fa-user-doctor"></i>
                </div>
                <div class="fw-bold text-dark" style="font-size: 13px">
                  Dokter<br />Profesional
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-md-3">
              <div class="t-card">
                <div class="t-icon">
                  <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="fw-bold text-dark" style="font-size: 13px">
                  Layanan<br />Terpercaya
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-md-3">
              <div class="t-card">
                <div class="t-icon">
                  <i class="fa-solid fa-house-medical"></i>
                </div>
                <div class="fw-bold text-dark" style="font-size: 13px">
                  Klinik<br />Nyaman
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-md-3">
              <div class="t-card">
                <div class="t-icon"><i class="fa-solid fa-heart"></i></div>
                <div class="fw-bold text-dark" style="font-size: 13px">
                  Perawatan<br />Berkualitas
                </div>
              </div>
            </div>
          </div>

          <!-- Layanan & Jadwal Row -->
          <div class="row g-4 mb-4">
            <!-- Layanan Kami -->
            <div class="col-md-6">
              <div class="panel-card h-100">
                <h5 class="fw-bold text-dark mb-4">Layanan Kami</h5>

                <div class="service-item">
                  <div class="t-icon me-3" style="width: 35px; height: 35px">
                    <i class="fa-solid fa-calendar-check fs-6"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="fw-bold text-dark mb-1">Janji Temu</h6>
                    <p class="text-muted small mb-0">
                      Buat janji temu dengan dokter.
                    </p>
                  </div>
                  <a href="#" class="text-decoration-none text-muted"
                    ><i class="fa-solid fa-arrow-right"></i
                  ></a>
                </div>

                <div class="service-item">
                  <div class="t-icon me-3" style="width: 35px; height: 35px">
                    <i class="fa-solid fa-comments fs-6"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="fw-bold text-dark mb-1">Konsultasi Online</h6>
                    <p class="text-muted small mb-0">
                      Konsultasi via chat/video.
                    </p>
                  </div>
                  <a
                    href="tanya-dokter"
                    class="text-decoration-none text-muted"
                    ><i class="fa-solid fa-arrow-right"></i
                  ></a>
                </div>

                <div class="service-item border-0 pb-0">
                  <div class="t-icon me-3" style="width: 35px; height: 35px">
                    <i class="fa-solid fa-notes-medical fs-6"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="fw-bold text-dark mb-1">Medical Check Up</h6>
                    <p class="text-muted small mb-0">
                      Pemeriksaan kesehatan lengkap.
                    </p>
                  </div>
                  <a href="#" class="text-decoration-none text-muted"
                    ><i class="fa-solid fa-arrow-right"></i
                  ></a>
                </div>
              </div>
            </div>

            <!-- Jadwal Janji Temu -->
            <div class="col-md-6">
              <div class="panel-card h-100">
                <div
                  class="d-flex justify-content-between align-items-center mb-4"
                >
                  <h5 class="fw-bold text-dark mb-0">Jadwal Janji Temu</h5>
                  <a href="#" class="text-decoration-none small text-muted"
                    >Lihat Semua →</a
                  >
                </div>

                <div class="appointment-card">
                  <div class="date-chip">
                    <div class="fw-bold fs-5">12</div>
                    <div class="small">JUN</div>
                  </div>
                  <div>
                    <h6 class="fw-bold text-dark mb-1">dr. Amanda Putri</h6>
                    <p class="text-muted small mb-1">Spesialis Umum</p>
                    <p class="text-muted small mb-0">◷ 09.00 - 09.30 WIB</p>
                  </div>
                </div>

                <div class="appointment-card">
                  <div class="date-chip" style="background-color: #606672">
                    <div class="fw-bold fs-5">20</div>
                    <div class="small">JUN</div>
                  </div>
                  <div>
                    <h6 class="fw-bold text-dark mb-1">dr. Budi Santoso</h6>
                    <p class="text-muted small mb-1">Spesialis Jantung</p>
                    <p class="text-muted small mb-0">◷ 10.00 - 10.30 WIB</p>
                  </div>
                </div>

                <button
                  class="btn btn-outline-primary w-100 mt-3 rounded-pill fw-bold"
                  style="
                    border-color: var(--primary-color);
                    color: var(--primary-color);
                  "
                >
                  Buat Janji Baru
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div class="col-xl-3 col-lg-4">
          <!-- Profile Panel -->
          <div class="right-sidebar text-center pt-4">
            <div class="position-relative d-inline-block mb-3">
              <?php 
                $foto_profil = isset($_SESSION['user_foto']) && !empty($_SESSION['user_foto']) ? 'asset/img/profil/' . $_SESSION['user_foto'] : 'asset/img/icon-female.png';
              ?>
              <img
                src="<?= htmlspecialchars($foto_profil) ?>"
                alt="Profile"
                class="rounded-circle border border-3"
                style="
                  width: 90px;
                  height: 90px;
                  object-fit: cover;
                  border-color: #c8d9f2 !important;
                "
              />
              <div
                class="position-absolute bottom-0 end-0 bg-white rounded-circle p-1 shadow-sm d-flex align-items-center justify-content-center"
                style="width: 28px; height: 28px"
              >
                <i
                  class="fa-solid fa-plus text-primary-custom"
                  style="font-size: 12px"
                ></i>
              </div>
            </div>

            <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($is_logged_in ? $user_nama : 'Guest') ?></h5>
            <p class="text-muted small mb-3">
              <?= htmlspecialchars($is_logged_in && isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'guest@diveramedical.com') ?>
            </p>
            <a
              href="#"
              class="text-primary-custom text-decoration-none fw-bold small d-inline-block mb-4"
              >Edit Profil</a
            >

            <div class="text-start mt-2">
              <a href="#" class="profile-menu-link">
                <div class="d-flex align-items-center">
                  <div
                    class="menu-icon"
                    style="border-color: var(--primary-color)"
                  ></div>
                  <span class="fw-bold" style="font-size: 13px"
                    >Riwayat Janji Temu</span
                  >
                </div>
                <i class="fa-solid fa-chevron-right small"></i>
              </a>
              <a href="tanya-dokter" class="profile-menu-link">
                <div class="d-flex align-items-center">
                  <div
                    class="menu-icon"
                    style="border-color: var(--primary-color)"
                  ></div>
                  <span class="fw-bold" style="font-size: 13px"
                    >Konsultasi Online</span
                  >
                </div>
                <i class="fa-solid fa-chevron-right small"></i>
              </a>
              <a href="#" class="profile-menu-link">
                <div class="d-flex align-items-center">
                  <div
                    class="menu-icon"
                    style="border-color: var(--primary-color)"
                  ></div>
                  <span class="fw-bold" style="font-size: 13px"
                    >Hasil Pemeriksaan</span
                  >
                </div>
                <i class="fa-solid fa-chevron-right small"></i>
              </a>
              <a href="kalender-kehamilan" class="profile-menu-link">
                <div class="d-flex align-items-center">
                  <div
                    class="menu-icon"
                    style="border-color: var(--primary-color)"
                  ></div>
                  <span class="fw-bold" style="font-size: 13px"
                    >Data Kesehatan</span
                  >
                </div>
                <i class="fa-solid fa-chevron-right small"></i>
              </a>
              <a href="#" class="profile-menu-link active">
                <div class="d-flex align-items-center">
                  <div class="menu-icon"></div>
                  <span class="fw-bold" style="font-size: 13px"
                    >Asuransi Aktif</span
                  >
                </div>
                <i class="fa-solid fa-chevron-right small"></i>
              </a>
              <a href="#" class="profile-menu-link">
                <div class="d-flex align-items-center">
                  <div
                    class="menu-icon"
                    style="border-color: var(--primary-color)"
                  ></div>
                  <span class="fw-bold" style="font-size: 13px"
                    >Pengaturan</span
                  >
                </div>
                <i class="fa-solid fa-chevron-right small"></i>
              </a>
            </div>
          </div>

          <!-- Articles Panel -->
          <div class="right-sidebar">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="fw-bold text-dark mb-0">Artikel Kesehatan</h5>
              <a
                href="kesehatan"
                class="text-primary-custom text-decoration-none small fw-bold"
                >Lihat Semua &rarr;</a
              >
            </div>

            <div class="d-flex mb-4">
              <div
                class="rounded-4 me-3 d-flex align-items-center justify-content-center flex-shrink-0"
                style="
                  width: 72px;
                  height: 60px;
                  background-color: #f9b8cf;
                  color: white;
                  font-size: 24px;
                "
              >
                <i class="fa-solid fa-staff-snake"></i>
              </div>
              <div>
                <h6
                  class="fw-bold text-dark mb-1"
                  style="font-size: 13px; line-height: 1.4"
                >
                  5 Tips Menjaga Kesehatan Jantung
                </h6>
                <p
                  class="text-muted mb-1"
                  style="font-size: 11px; line-height: 1.3"
                >
                  Jantung sehat untuk hidup lebih berkualitas.
                </p>
                <p class="text-muted small mb-0" style="font-size: 11px">
                  10 Juni 2024
                </p>
              </div>
            </div>

            <div class="d-flex mb-4">
              <div
                class="rounded-4 me-3 d-flex align-items-center justify-content-center flex-shrink-0"
                style="
                  width: 72px;
                  height: 60px;
                  background-color: #c8d9f2;
                  color: white;
                  font-size: 24px;
                "
              >
                <i class="fa-solid fa-moon"></i>
              </div>
              <div>
                <h6
                  class="fw-bold text-dark mb-1"
                  style="font-size: 13px; line-height: 1.4"
                >
                  Pentingnya Tidur Cukup
                </h6>
                <p
                  class="text-muted mb-1"
                  style="font-size: 11px; line-height: 1.3"
                >
                  Tidur berkualitas tingkatkan daya tahan tubuh.
                </p>
                <p class="text-muted small mb-0" style="font-size: 11px">
                  8 Juni 2024
                </p>
              </div>
            </div>

            <div class="d-flex">
              <div
                class="rounded-4 me-3 d-flex align-items-center justify-content-center flex-shrink-0"
                style="
                  width: 72px;
                  height: 60px;
                  background-color: #cdeed8;
                  color: white;
                  font-size: 24px;
                "
              >
                <i class="fa-solid fa-spa"></i>
              </div>
              <div>
                <h6
                  class="fw-bold text-dark mb-1"
                  style="font-size: 13px; line-height: 1.4"
                >
                  Makanan Kacang Bergizi
                </h6>
                <p
                  class="text-muted mb-1"
                  style="font-size: 11px; line-height: 1.3"
                >
                  Berikut kacang-kacangan bergizi yang bisa anda makan.
                </p>
                <p class="text-muted small mb-0" style="font-size: 11px">
                  6 Juni 2024
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Stats -->
      <div class="bottom-stats d-flex flex-wrap gap-4 mt-2 mb-4">
        <div class="stat-item flex-grow-1">
          <div class="t-icon"><i class="fa-regular fa-clock"></i></div>
          <div>
            <div class="fw-bold text-dark">08.00 - 20.00</div>
            <div class="text-muted small">Buka Setiap Hari</div>
          </div>
        </div>
        <div class="stat-item flex-grow-1">
          <div class="t-icon"><i class="fa-solid fa-user-doctor"></i></div>
          <div>
            <div class="fw-bold text-dark">100+</div>
            <div class="text-muted small">Dokter Profesional</div>
          </div>
        </div>
        <div class="stat-item flex-grow-1">
          <div class="t-icon"><i class="fa-solid fa-users"></i></div>
          <div>
            <div class="fw-bold text-dark">10K+</div>
            <div class="text-muted small">Pasien Terpercaya</div>
          </div>
        </div>
        <div class="stat-item flex-grow-1">
          <div class="t-icon"><i class="fa-solid fa-star"></i></div>
          <div>
            <div class="fw-bold text-dark">4.9/5</div>
            <div class="text-muted small">Rating Dokter</div>
          </div>
        </div>
      </div>

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
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      const themeToggle = document.getElementById("themeToggle");
      const sidebar = document.querySelector(".sidebar");
      const mainContent = document.querySelector(".main-content");
      const mobileMenuToggle = document.getElementById("mobileMenuToggle");
      const desktopMenuToggle = document.getElementById("desktopMenuToggle");

      // Sidebar toggles
      mobileMenuToggle.addEventListener("click", () => {
        sidebar.classList.toggle("show");
      });

      desktopMenuToggle.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
        mainContent.classList.toggle("expanded");
      });

      themeToggle.addEventListener("click", () => {
        const currentTheme =
          document.documentElement.getAttribute("data-bs-theme");
        const newTheme = currentTheme === "dark" ? "light" : "dark";
        document.documentElement.setAttribute("data-bs-theme", newTheme);
        themeToggle.textContent = newTheme === "dark" ? "☀️" : "🌓";

        // Fix specific text elements for dark mode visibility
        document.querySelectorAll(".text-dark, .text-white").forEach((el) => {
          if (!el.classList.contains("text-hero-custom")) {
            if (newTheme === "dark") {
              el.classList.remove("text-dark");
              el.classList.add("text-white");
            } else {
              el.classList.remove("text-white");
              el.classList.add("text-dark");
            }
          }
        });

        // Hero text specifically
        document.querySelectorAll(".text-hero-custom").forEach((el) => {
          if (newTheme === "dark") {
            el.style.color = "#ffffff";
          } else {
            el.style.color = "#4A1C2F";
          }
        });
      });

      // Local JS Search Logic
      const searchInput = document.getElementById("globalSearchInput");
      const searchResults = document.getElementById("searchResults");

      const pages = [
        { title: "Beranda / Dashboard", url: "index.php", icon: "fa-house-chimney", category: "Menu Utama" },
        { title: "Layanan Homecare", url: "homecare.php", icon: "fa-hand-holding-medical", category: "Layanan" },
        { title: "Artikel Kesehatan", url: "kesehatan.php", icon: "fa-newspaper", category: "Informasi" },
        { title: "Perawatan Kulit & Estetika", url: "perawatan-kulit.php", icon: "fa-spa", category: "Layanan Khusus" },
        { title: "Obat & Vitamin", url: "obat-vitamin.php", icon: "fa-capsules", category: "Farmasi" },
        { title: "Kalender Kehamilan", url: "kalender-kehamilan.php", icon: "fa-heart-pulse", category: "Data Kesehatan" },
        { title: "Tanya Dokter", url: "tanya-dokter.php", icon: "fa-user-doctor", category: "Konsultasi" },
        { title: "Program Diet / Weight Loss", url: "program-diet.php", icon: "fa-apple-whole", category: "Layanan Khusus" },
        { title: "Pengaturan Akun", url: "login.php", icon: "fa-gear", category: "Sistem" }
      ];

      searchInput.addEventListener("input", function() {
        const query = this.value.trim().toLowerCase();
        
        if (query.length > 0) {
          const results = pages.filter(page => 
            page.title.toLowerCase().includes(query) || 
            page.category.toLowerCase().includes(query)
          );
          
          searchResults.innerHTML = '';
          if (results.length > 0) {
            results.forEach(item => {
              const link = document.createElement('a');
              link.href = item.url;
              link.className = "d-flex align-items-center gap-3 p-3 text-decoration-none border-bottom";
              link.style.color = "inherit";
              
              link.innerHTML = `
                <div class="t-icon bg-light text-primary-custom" style="width: 35px; height: 35px; flex-shrink: 0;">
                  <i class="fa-solid ${item.icon}"></i>
                </div>
                <div>
                  <div class="fw-bold" style="font-size: 14px; color: #131724;">${item.title}</div>
                  <div class="text-muted" style="font-size: 11px;">${item.category}</div>
                </div>
              `;
              
              link.addEventListener("mouseenter", () => link.style.backgroundColor = "#f8f9fa");
              link.addEventListener("mouseleave", () => link.style.backgroundColor = "transparent");
              
              searchResults.appendChild(link);
            });
            searchResults.classList.remove("d-none");
          } else {
            searchResults.innerHTML = `<div class="p-4 text-center text-muted small">Tidak ada hasil ditemukan untuk "<strong>${query}</strong>"</div>`;
            searchResults.classList.remove("d-none");
          }
        } else {
          searchResults.classList.add("d-none");
        }
      });

      // Hide results when clicking outside
      document.addEventListener("click", function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
          searchResults.classList.add("d-none");
        }
      });
    </script>
  </body>
</html>


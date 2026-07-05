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
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="asset/img/logo.png" type="image/png" />
    <title>Weight Loss - DiVera Medical</title>
    <style>
      :root {
        --primary-pink: #e91e63;
        --white: #ffffff;
        --bg-color: #f8f9fa;
        --text-main: #131724;
        --text-muted: #606672;
      }
      body {
        margin: 0;
        font-family: "Inter", sans-serif;
        background-color: var(--bg-color);
        display: flex;
        min-height: 100vh;
      }
      .sidebar {
        width: 260px;
        background: var(--white);
        border-right: 1px solid #ebe6e9;
        padding: 20px;
        display: flex;
        flex-direction: column;
      }
      .sidebar .logo {
        width: 100%;
        max-width: 150px;
        margin-bottom: 30px;
      }
      .nav-links {
        display: flex;
        flex-direction: column;
        gap: 15px;
      }
      .nav-links a {
        text-decoration: none;
        color: var(--text-muted);
        font-weight: 500;
        padding: 10px 15px;
        border-radius: 8px;
        transition: all 0.3s;
      }
      .nav-links a:hover,
      .nav-links a.active {
        background: #fff1f7;
        color: var(--primary-pink);
      }
      .main-content {
        flex: 1;
        padding: 30px 40px;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
      }
      header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
      }
      .search-bar {
        padding: 10px 20px;
        border-radius: 20px;
        border: 1px solid #ebe6e9;
        width: 300px;
      }
      .user-info {
        display: flex;
        align-items: center;
        gap: 15px;
      }
      .user-info img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
      }
      .page-banner {
        background: linear-gradient(135deg, #fff1f7, #fde7ef);
        padding: 40px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 30px;
      }
      .page-banner h1 {
        color: var(--primary-pink);
        margin: 0 0 10px 0;
        font-size: 32px;
      }
      .page-banner p {
        color: var(--text-muted);
      }
      .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
      }
      .card {
        background: var(--white);
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid #fff8fb;
      }
      .card h3 {
        color: var(--text-main);
        margin-top: 0;
      }
      .card p {
        color: var(--text-muted);
        font-size: 14px;
      }
      .btn {
        background: var(--primary-pink);
        color: var(--white);
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
        font-weight: bold;
        border: none;
        cursor: pointer;
      }
      .btn:hover {
        background: #d81b60;
      }
    </style>
  </head>
  <body>
    <aside class="sidebar">
      <img src="asset/img/logo.png" alt="Logo" class="logo" />
      <nav class="nav-links">
        <a href="index">Beranda</a>
        <a href="homecare">Homecare</a>
        <a href="kesehatan">Kesehatan</a>
        <a href="perawatan-kulit">Perawatan Kulit</a>
        <a href="obat-vitamin">Obat & Vitamin</a>
        <a href="tanya-dokter">Tanya Dokter</a>
        <a href="weight-loss" class="active">Weight Loss</a>
        <a href="artikel-kalender-kehamilan">Artikel</a>
      </nav>
    </aside>
    <main class="main-content">
      <header>
        <input
          type="search"
          class="search-bar"
          placeholder="Cari program diet..."
        />
        <div class="user-info">
          <a href="<?= $is_logged_in ? 'keranjang' : 'login' ?>" class="text-dark position-relative me-3" title="Keranjang" style="text-decoration: none;">
            <i class="fa-solid fa-cart-shopping fs-5" style="color: #606672;"></i>
          </a>
          <span><?= htmlspecialchars($is_logged_in ? $user_nama : 'Guest') ?></span>
          <?php 
            $foto_profil = isset($_SESSION['user_foto']) && !empty($_SESSION['user_foto']) ? 'asset/img/profil/' . $_SESSION['user_foto'] : 'asset/img/icon-female.png';
          ?>
          <img src="<?= htmlspecialchars($foto_profil) ?>" alt="Profile" style="object-fit: cover;" />
        </div>
      </header>
      <section class="page-banner">
        <div>
          <h1>Weight Loss Program</h1>
          <p>
            Program penurunan berat badan yang aman dan diawasi oleh dokter
            gizi.
          </p>
          <button class="btn">Daftar Program</button>
        </div>
        <img
          src="asset/img/female-doctor.png"
          alt="Doctor"
          style="height: 150px"
        />
      </section>
      <div class="card-grid">
        <div class="card">
          <img
            src="asset/img/600x400.jpg"
            alt="Diet"
            style="
              width: 100%;
              border-radius: 10px;
              margin-bottom: 15px;
              height: 150px;
              object-fit: cover;
            "
          />
          <h3>Konsultasi Ahli Gizi</h3>
          <p>Rencanakan pola makan ideal sesuai kondisi tubuh Anda.</p>
          <a href="#" class="btn" style="margin-top: 10px">Detail</a>
        </div>
        <div class="card">
          <img
            src="asset/img/600x400.jpg"
            alt="Diet"
            style="
              width: 100%;
              border-radius: 10px;
              margin-bottom: 15px;
              height: 150px;
              object-fit: cover;
            "
          />
          <h3>Paket Diet Sehat</h3>
          <p>Katering diet sehat dan bernutrisi yang dikirim ke rumah Anda.</p>
          <a href="#" class="btn" style="margin-top: 10px">Detail</a>
        </div>
        <div class="card">
          <img
            src="asset/img/600x400.jpg"
            alt="Diet"
            style="
              width: 100%;
              border-radius: 10px;
              margin-bottom: 15px;
              height: 150px;
              object-fit: cover;
            "
          />
          <h3>Pemantauan Rutin</h3>
          <p>
            Evaluasi progres berat badan Anda secara berkala bersama tim medis.
          </p>
          <a href="#" class="btn" style="margin-top: 10px">Detail</a>
        </div>
      </div>
    </main>
  </body>
</html>


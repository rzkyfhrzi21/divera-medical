<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash'] = ['status' => 'error', 'message' => 'Anda harus login.'];
    header("Location: login");
    exit;
}

$user_nama = $_SESSION['user_nama'];
$dashboard_url = 'dashboard/pasien/';
if ($_SESSION['user_role'] == 'admin') {
    $dashboard_url = 'dashboard/admin-dashboard.php';
} else if ($_SESSION['user_role'] == 'dokter') {
    $dashboard_url = 'dashboard/dokter/';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Alamat - DiVera Medical</title>
    <link rel="icon" href="asset/img/logo.png" type="image/png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body { background-color: #f8f9fa; }
        .text-primary-custom { color: #E91E63 !important; }
        .bg-primary-custom { background-color: #E91E63 !important; }
        .btn-primary-custom { background-color: #E91E63; color: white; border: none; }
        .btn-primary-custom:hover { background-color: #D81B60; color: white; }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #dee2e6;
            background-color: white;
            margin-bottom: 20px;
        }
        .step {
            display: flex;
            align-items: center;
            color: #adb5bd;
            font-weight: 500;
        }
        .step.active {
            color: #E91E63;
        }
        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid #adb5bd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 14px;
        }
        .step.active .step-circle {
            background-color: #E91E63;
            color: white;
            border-color: #E91E63;
        }
        .step.completed {
            color: #E91E63;
        }
        .step.completed .step-circle {
            border-color: #E91E63;
            color: #E91E63;
            background-color: #fce4ec;
        }
        .step-line {
            width: 50px;
            height: 2px;
            background-color: #dee2e6;
            margin: 0 15px;
        }
        
        .address-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 0 auto;
        }
        .address-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }
        .map-container {
            height: 200px;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            position: relative;
        }
        .map-container iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }
        .target-icon {
            position: absolute;
            bottom: -20px;
            right: 20px;
            background: white;
            width: 40px; height: 40px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            color: #007bff;
        }
        .address-body {
            padding: 30px 20px;
        }
    </style>
</head>
<body>

    <!-- Header Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center text-primary-custom fw-bold" href="index">
                <img src="asset/img/logo.png" alt="Logo" height="30" class="me-2">
            </a>
            <div class="ms-auto d-flex align-items-center">
            <a href="<?= $dashboard_url ?>" class="text-decoration-none d-flex align-items-center">
                <?php $foto_profil = isset($_SESSION['user_foto']) && !empty($_SESSION['user_foto']) ? 'asset/img/profil/' . $_SESSION['user_foto'] : 'asset/img/icon-female.png'; ?>
                                <img src="<?= htmlspecialchars($foto_profil) ?>" alt="Profile" class="rounded-circle border me-2" width="35" height="35" style="object-fit: cover;">
                <span class="fw-bold fs-6 text-dark"><?= htmlspecialchars($user_nama) ?></span>
            </a>
        </div>
        </div>
    </nav>

    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step completed">
            <div class="step-circle"><i class="fa-solid fa-check"></i></div> Keranjang
        </div>
        <div class="step-line"></div>
        <div class="step active">
            <div class="step-circle">2</div> Alamat
        </div>
        <div class="step-line"></div>
        <div class="step text-muted">
            <div class="step-circle">3</div> Pembayaran
        </div>
    </div>

    <!-- Main Content -->
    <div class="container pb-5 mt-4">
        <div class="address-card">
            <div class="address-header">
                <h4 class="fw-bold mb-0">Pilih Alamat</h4>
            </div>
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.267323867623!2d106.82705291476906!3d-6.228442795491124!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e46c7694cd%3A0x89228807d72c1c65!2sJl.%20Setia%20Budi%20Sel.%2C%20RT.10%2FRW.7%2C%20Kuningan%2C%20Karet%2C%20Kecamatan%20Setiabudi%2C%20Kota%20Jakarta%20Selatan%2C%20Daerah%20Khusus%20Ibukota%20Jakarta!5e0!3m2!1sid!2sid!4v1684305884931!5m2!1sid!2sid" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <div class="target-icon"><i class="fa-solid fa-crosshairs"></i></div>
            </div>
            <div class="address-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold m-0 text-secondary">Jalan Setia Budi Selatan</h5>
                    <a href="#" class="text-primary-custom text-decoration-none fw-bold"><i class="fa-solid fa-magnifying-glass me-1"></i> Cari</a>
                </div>
                <p class="text-muted mb-4">
                    Jl. Setia Budi Selatan No.10, RT.10/RW.7, Kuningan, Karet, Kecamatan Setiabudi, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta
                </p>
                <a href="bayar.php" class="btn btn-primary-custom w-100 py-3 fw-bold rounded">Konfirmasi</a>
            </div>
        </div>
    </div>

</body>
</html>

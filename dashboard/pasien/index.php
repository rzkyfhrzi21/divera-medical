<?php
session_start();

// Cek apakah user sudah login dan rolenya pasien
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pasien') {
    $_SESSION['flash'] = ['status' => 'error', 'message' => 'Anda harus login sebagai pasien.'];
    header("Location: ../../login");
    exit;
}

// Ambil flash message jika ada
$flash = null;
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

// Koneksi database
require_once '../../config/koneksi.php';
$user_nama = $_SESSION['user_nama'];

// Ambil page dari URL, default 'Beranda'
$page = isset($_GET['page']) ? $_GET['page'] : 'Beranda';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page) ?> - Pasien DiVera</title>
    <link rel="icon" href="../../asset/img/logo.png" type="image/png" />
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .text-primary-custom { color: #E91E63 !important; }
        .bg-primary-custom { background-color: #E91E63 !important; }
        .btn-primary-custom { background-color: #E91E63; color: white; border: none; }
        .btn-primary-custom:hover { background-color: #D81B60; color: white; }

        .header-bar {
            background: linear-gradient(135deg, #E91E63, #AD1457);
            color: white; padding: 20px 0;
        }

        .sidebar-menu {
            background: white; border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .sidebar-item {
            padding: 12px 18px; color: #555; text-decoration: none;
            display: block; border-left: 3px solid transparent; font-size: 14px;
            transition: all 0.2s;
        }
        .sidebar-item:hover, .sidebar-item.active {
            color: #E91E63; background-color: #fce4ec;
            border-left-color: #E91E63; font-weight: 600;
        }
        .sidebar-title {
            font-size: 11px; font-weight: bold; color: #999; letter-spacing: 1px;
            padding: 18px 18px 5px 18px; margin: 0; text-transform: uppercase;
        }

        .content-card {
            background: white; border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 25px;
        }

        .quick-action {
            border: 1px solid #f0e0e8; border-radius: 12px; padding: 20px;
            text-align: center; text-decoration: none; color: #333;
            transition: all 0.3s; display: block; background: white;
        }
        .quick-action:hover {
            border-color: #E91E63; transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(233, 30, 99, 0.15); color: #E91E63;
        }
        .quick-action .qa-icon {
            width: 50px; height: 50px; border-radius: 50%;
            background-color: #fce4ec; color: #E91E63;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 10px; font-size: 20px;
        }

        .badge-status { font-size: 11px; padding: 4px 10px; border-radius: 20px; }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header-bar">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="../../index" class="text-white text-decoration-none">
                        <div class="fw-bold fs-5 me-3"><i class="fa-solid fa-heart-pulse me-2"></i>DiVera Medical</div>
                    </a>
                    <span class="fs-6 border-start ps-3 border-white border-opacity-50 d-none d-md-inline">Dashboard Pasien</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="fw-bold small"><?= htmlspecialchars($user_nama) ?></span>
                    <a href="../../config/function_auth.php?action=logout" class="btn btn-sm btn-outline-light rounded-pill px-3">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                    </a>
                </div>
            </div>
            <!-- Greeting inside header -->
            <div class="mt-3">
                <h4 class="fw-bold mb-1">Halo, <?= htmlspecialchars($user_nama) ?>! 👋</h4>
                <p class="mb-0 small opacity-75">Semoga hari Anda menyenangkan. Jaga kesehatan selalu!</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid px-4 mt-4 mb-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2">
                <div class="sidebar-menu pb-3">
                    <p class="sidebar-title">Menu Utama</p>
                    <a href="index?page=Beranda" class="sidebar-item <?= $page == 'Beranda' ? 'active' : '' ?>">
                        <i class="fa-solid fa-house fa-fw me-2"></i> Beranda
                    </a>

                    <p class="sidebar-title">Layanan</p>
                    <a href="../../tanya-dokter" class="sidebar-item"><i class="fa-solid fa-user-doctor fa-fw me-2"></i> Konsultasi</a>
                    <a href="../../obat-vitamin" class="sidebar-item"><i class="fa-solid fa-pills fa-fw me-2"></i> Beli Obat</a>
                    <a href="index?page=Janji Temu" class="sidebar-item <?= $page == 'Janji Temu' ? 'active' : '' ?>">
                        <i class="fa-solid fa-calendar-check fa-fw me-2"></i> Janji Temu
                    </a>

                    <p class="sidebar-title">Akun</p>
                    <a href="index?page=Profil Saya" class="sidebar-item <?= $page == 'Profil Saya' ? 'active' : '' ?>">
                        <i class="fa-solid fa-user-pen fa-fw me-2"></i> Profil Saya
                    </a>
                </div>
            </div>

            <!-- Content Area (Routing) -->
            <div class="col-md-9 col-lg-10">
                <!-- jQuery & DataTables JS -->
                <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

                <?php
                switch ($page) {
                    case 'Beranda':
                        include 'dashboard.php';
                        break;
                    case 'Janji Temu':
                        include 'janji_temu.php';
                        break;
                    case 'Profil Saya':
                        echo '<div class="content-card"><h5>Profil Saya</h5><p>Halaman pengaturan profil belum diimplementasikan sepenuhnya.</p></div>';
                        break;
                    default:
                        include 'dashboard.php';
                        break;
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    <?php if ($flash): ?>
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

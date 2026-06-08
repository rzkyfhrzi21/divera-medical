<?php
session_start();

// Cek apakah user sudah login dan rolenya dokter
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'dokter') {
    $_SESSION['flash'] = ['status' => 'error', 'message' => 'Anda harus login sebagai dokter.'];
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

// Ambil page dari URL, default 'Dashboard'
$page = isset($_GET['page']) ? $_GET['page'] : 'Dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page) ?> - Dokter DiVera</title>
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
        body { background-color: #f5f5f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .text-primary-custom { color: #E91E63 !important; }
        .bg-primary-custom { background-color: #E91E63 !important; }
        .btn-primary-custom { background-color: #E91E63; color: white; border: none; }
        .btn-primary-custom:hover { background-color: #D81B60; color: white; }

        .admin-header { background-color: #E91E63; color: white; }

        .sidebar-menu {
            background: white; border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            min-height: calc(100vh - 120px);
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
            background: white; border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 25px;
        }
        .stat-card {
            border-radius: 12px; padding: 20px; color: white;
            position: relative; overflow: hidden;
        }
        .stat-card .stat-icon {
            position: absolute; right: 15px; top: 50%; transform: translateY(-50%);
            font-size: 40px; opacity: 0.3;
        }
        .stat-card h3 { font-size: 28px; font-weight: 700; margin: 0; }
        .stat-card p { margin: 0; font-size: 13px; opacity: 0.9; }

        .badge-status { font-size: 11px; padding: 4px 10px; border-radius: 20px; }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="admin-header py-2">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="../../index" class="text-white text-decoration-none">
                    <div class="fw-bold fs-5 me-3"><i class="fa-solid fa-stethoscope me-2"></i>DiVera Medical</div>
                </a>
                <span class="fs-6 border-start ps-3 border-white border-opacity-50">Dashboard Dokter</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="fw-bold small"><?= htmlspecialchars($user_nama) ?></span>
                <a href="../../config/function_auth.php?action=logout" class="btn btn-sm btn-outline-light rounded-pill px-3">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container-fluid px-4 mt-4 mb-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2">
                <div class="sidebar-menu pb-3">
                    <p class="sidebar-title">Menu Utama</p>
                    <a href="index?page=Dashboard" class="sidebar-item <?= $page == 'Dashboard' ? 'active' : '' ?>">
                        <i class="fa-solid fa-chart-line fa-fw me-2"></i> Dashboard
                    </a>

                    <p class="sidebar-title">Kelola</p>
                    <a href="index?page=Janji Temu" class="sidebar-item <?= $page == 'Janji Temu' ? 'active' : '' ?>">
                        <i class="fa-solid fa-calendar-check fa-fw me-2"></i> Janji Temu
                    </a>
                    <a href="index?page=Pasien Saya" class="sidebar-item <?= $page == 'Pasien Saya' ? 'active' : '' ?>">
                        <i class="fa-solid fa-users fa-fw me-2"></i> Pasien Saya
                    </a>

                    <p class="sidebar-title">Akun</p>
                    <a href="index?page=Profil" class="sidebar-item <?= $page == 'Profil' ? 'active' : '' ?>">
                        <i class="fa-solid fa-user-pen fa-fw me-2"></i> Profil Saya
                    </a>
                </div>
            </div>

            <!-- Content Area (Routing) -->
            <div class="col-md-9 col-lg-10">
                <!-- jQuery & DataTables JS dipindah ke atas agar script di halaman child bisa memanggilnya -->
                <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
                
                <?php
                switch ($page) {
                    case 'Dashboard':
                        include 'dashboard.php';
                        break;
                    case 'Janji Temu':
                        include 'janji_temu.php';
                        break;
                    case 'Pasien Saya':
                        echo '<div class="content-card"><h5>Pasien Saya</h5><p>Halaman ini belum diimplementasikan sepenuhnya.</p></div>';
                        break;
                    case 'Profil':
                        echo '<div class="content-card"><h5>Profil Saya</h5><p>Halaman ini belum diimplementasikan sepenuhnya.</p></div>';
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

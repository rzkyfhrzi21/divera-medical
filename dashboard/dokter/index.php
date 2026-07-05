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
        body { background-color: #f5f6fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
        .text-primary-custom { color: #E91E63 !important; }
        .bg-primary-custom { background-color: #E91E63 !important; }
        .btn-primary-custom { background-color: #E91E63; color: white; border: none; }
        .btn-primary-custom:hover { background-color: #D81B60; color: white; }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: #1e2235;
            color: #b0b3c6;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            overflow-y: auto;
            z-index: 1000;
            transition: 0.3s;
        }
        .sidebar-brand {
            padding: 20px 20px 10px 20px;
            display: flex;
            align-items: center;
        }
        .sidebar-brand img {
            height: 40px;
        }
        .sidebar-title {
            font-size: 11px; font-weight: bold; color: #6c7293; letter-spacing: 1px;
            padding: 20px 20px 10px 20px; margin: 0; text-transform: uppercase;
        }
        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 0;
        }
        .sidebar-item {
            padding: 12px 20px; color: #b0b3c6; text-decoration: none;
            display: flex; align-items: center; font-size: 14px;
            transition: all 0.2s;
            margin: 0 15px;
            border-radius: 8px;
        }
        .sidebar-item:hover {
            color: white; background-color: rgba(255,255,255,0.05);
        }
        .sidebar-item.active {
            color: white; background-color: #E91E63;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: 0.3s;
        }

        /* Topbar */
        .topbar {
            background-color: white;
            height: 80px;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .search-bar {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 20px;
            padding: 8px 20px;
            width: 350px;
            font-size: 14px;
            outline: none;
        }
        .topbar-icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .bell-icon {
            width: 35px; height: 35px;
            border-radius: 50%;
            background-color: #fce4ec;
            color: #E91E63;
            display: flex; align-items: center; justify-content: center;
            position: relative;
        }

        .content-area {
            padding: 30px;
            flex-grow: 1;
        }

        .content-card {
            background: white; border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 25px;
        }
        .stat-card {
            border-radius: 12px; padding: 20px; color: white;
            position: relative; overflow: hidden;
            background: white; border: 1px solid #eee;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .stat-card .stat-icon {
            position: absolute; right: 15px; top: 50%; transform: translateY(-50%);
            font-size: 40px; opacity: 0.3;
        }
        .stat-card h3 { font-size: 28px; font-weight: 700; margin: 0; color: #333; }
        .stat-card p { margin: 0; font-size: 13px; color: #777; }
        .badge-status { font-size: 11px; padding: 4px 10px; border-radius: 20px; }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .search-bar { display: none; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <a href="../../index"><img src="../../asset/img/logo.png" alt="Logo"></a>
        </div>
        <p class="sidebar-title">DOKTER PANEL</p>
        <div class="sidebar-menu">
            <a href="index?page=Dashboard" class="sidebar-item <?= $page == 'Dashboard' ? 'active' : '' ?>">1. Dashboard</a>
            <a href="index?page=Janji Temu" class="sidebar-item <?= $page == 'Janji Temu' ? 'active' : '' ?>">2. Janji Temu</a>
            <a href="index?page=Pasien Saya" class="sidebar-item <?= $page == 'Pasien Saya' ? 'active' : '' ?>">3. Pasien Saya</a>
            <a href="index?page=Data Produk" class="sidebar-item <?= $page == 'Data Produk' ? 'active' : '' ?>">4. Data Produk</a>
            <a href="index?page=Profil" class="sidebar-item <?= $page == 'Profil' ? 'active' : '' ?>">5. Profil Saya</a>
            <a href="../../config/function_auth.php?action=logout" class="sidebar-item mt-4 text-danger">6. Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <h4 class="m-0 fw-bold d-none d-md-block"><?= htmlspecialchars($page) ?></h4>
            <button class="btn d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')"><i class="fa-solid fa-bars"></i></button>
            <div class="d-none d-lg-block">
                <input type="text" class="search-bar" placeholder="Cari data...">
            </div>
            <div class="topbar-icons">
                <div class="bell-icon"><i class="fa-solid fa-bell"></i></div>
                <div class="d-flex align-items-center gap-2">
                    <?php 
                    $user_foto_path = isset($_SESSION['user_foto']) && !empty($_SESSION['user_foto']) ? '../../asset/img/profil/' . $_SESSION['user_foto'] : '';
                    if ($user_foto_path && file_exists(__DIR__ . '/../../asset/img/profil/' . $_SESSION['user_foto'])): ?>
                        <img src="<?= htmlspecialchars($user_foto_path) ?>" alt="Profile" class="rounded-circle border" style="width: 35px; height: 35px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-primary-custom text-white d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px;">
                            <?= strtoupper(substr($user_nama, 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <span class="fw-bold fs-6 text-dark"><?= htmlspecialchars($user_nama) ?></span>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- jQuery & DataTables JS -->
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
                    include 'pasien_saya.php';
                    break;
                case 'Data Produk':
                    include 'data_produk.php';
                    break;
                case 'Profil':
                    include 'profil.php';
                    break;
                default:
                    include 'dashboard.php';
                    break;
            }
            ?>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    <?php if ($flash): ?>
    Swal.fire({
        icon: '<?= ($flash['status'] === 'success' || $flash['status'] === 'login_success') ? 'success' : 'error' ?>',
        title: '<?= ($flash['status'] === 'success' || $flash['status'] === 'login_success') ? 'Berhasil' : 'Gagal' ?>',
        text: '<?= htmlspecialchars($flash['message']) ?>',
        confirmButtonColor: '#E91E63'
    });
    <?php endif; ?>

    // Fitur Search Menu
    const searchBar = document.querySelector('.search-bar');
    if (searchBar) {
        searchBar.addEventListener('input', function(e) {
            let term = e.target.value.toLowerCase();
            document.querySelectorAll('.sidebar-item').forEach(item => {
                if (item.textContent.toLowerCase().includes(term)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    </script>
</body>
</html>

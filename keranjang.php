<?php
// keranjang.php
?>
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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - DiVera Medical</title>
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
        .step-line {
            width: 50px;
            height: 2px;
            background-color: #dee2e6;
            margin: 0 15px;
        }
        
        .cart-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #ebe6e9;
        }
        .qty-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #E91E63;
            background-color: white;
            color: #E91E63;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .summary-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #ebe6e9;
        }
    </style>
</head>
<body>

    <!-- Header Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center text-primary-custom fw-bold" href="index">
                <img src="asset/img/logo.png" alt="Logo" height="30" class="me-2"> DiVera Medical
            </a>
            <div class="ms-auto d-flex align-items-center">
            <a href="<?= $dashboard_url ?>" class="text-decoration-none d-flex align-items-center">
                <img src="asset/img/icon-female.png" alt="Profile" class="rounded-circle border me-2" width="35" height="35" style="object-fit: cover;">
                <span class="fw-bold fs-6 text-dark"><?= htmlspecialchars($user_nama) ?></span>
            </a>
        </div>
        </div>
    </nav>

    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step text-muted">
            <div class="step-circle">1</div> Alamat
        </div>
        <div class="step-line"></div>
        <div class="step active">
            <div class="step-circle">2</div> Keranjang
        </div>
        <div class="step-line"></div>
        <div class="step text-muted">
            <div class="step-circle">3</div> Pembayaran
        </div>
    </div>

    <!-- Main Content -->
    <div class="container pb-5">
        <div class="row">
            <!-- Keranjang Items -->
            <div class="col-lg-8">
                <h5 class="fw-bold mb-3">Daftar Pesanan</h5>
                
                <div class="cart-card d-flex align-items-center">
                    <img src="asset/img/female-doctor.png" alt="Product" width="80" height="80" class="rounded border me-3" style="object-fit: cover;">
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1">Neurobion Forte 10 Tablet</h6>
                        <p class="text-muted small mb-0">Per Strip</p>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold mb-2">Rp 57.000</div>
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <i class="fa-regular fa-trash-can text-muted" style="cursor: pointer;"></i>
                            <div class="qty-btn ms-2">-</div>
                            <span class="fw-bold">1</span>
                            <div class="qty-btn">+</div>
                        </div>
                    </div>
                </div>

                <!-- Sering Dibeli Berbarengan -->
                <h6 class="fw-bold mt-4 mb-3">Sering Dibeli Berbarengan</h6>
                <div class="d-flex gap-3 overflow-auto pb-3">
                    <div class="border rounded p-2 text-center" style="min-width: 140px; background: white;">
                        <img src="asset/img/female-doctor.png" alt="Prod" width="60" height="60" class="mb-2">
                        <div class="small fw-bold text-truncate" style="font-size: 11px;">Tolak Angin Cair</div>
                        <div class="small text-muted mb-2" style="font-size: 11px;">Rp 63.700</div>
                        <button class="btn btn-primary-custom btn-sm w-100 p-0" style="height: 25px;">+</button>
                    </div>
                    <div class="border rounded p-2 text-center" style="min-width: 140px; background: white;">
                        <img src="asset/img/female-doctor.png" alt="Prod" width="60" height="60" class="mb-2">
                        <div class="small fw-bold text-truncate" style="font-size: 11px;">Betadine Mouthwash</div>
                        <div class="small text-muted mb-2" style="font-size: 11px;">Rp 31.400</div>
                        <button class="btn btn-primary-custom btn-sm w-100 p-0" style="height: 25px;">+</button>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Pembayaran -->
            <div class="col-lg-4">
                <div class="summary-card">
                    <h6 class="fw-bold mb-3">Promo / Kode Diskon</h6>
                    <div class="d-flex gap-2 mb-4">
                        <input type="text" class="form-control form-control-sm" placeholder="Masukkan kode">
                        <button class="btn btn-light border btn-sm">Terapkan</button>
                    </div>

                    <h6 class="fw-bold mb-3">Ringkasan Pembayaran</h6>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Keranjang (1 Item)</span>
                        <span>Rp 57.000</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Total Pengiriman</span>
                        <span>Rp 23.000</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small">
                        <span class="text-muted">Biaya Layanan</span>
                        <span>Rp 2.000</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Total Tagihan</span>
                        <span class="fw-bold fs-5">Rp 82.000</span>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-bold">Alamat Pengiriman</span>
                            <a href="#" class="text-primary-custom text-decoration-none small fw-bold">Ubah</a>
                        </div>
                        <div class="small fw-bold">Jalan Setia Budi Selatan</div>
                        <p class="small text-muted mb-2">Jl. Setia Budi Selatan No.10, RT.10/RW.7, Kuningan, Karet...</p>
                        <input type="text" class="form-control form-control-sm" placeholder="Catatan (Contoh: Rumah no. 6)">
                    </div>

                    <button class="btn btn-primary-custom w-100 py-2 fw-bold" onclick="window.location.href='keranjang-pembayaran.php'">Berikutnya</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

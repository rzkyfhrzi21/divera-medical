<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash'] = ['status' => 'error', 'message' => 'Anda harus login.'];
    header("Location: login");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_nama = $_SESSION['user_nama'];
$dashboard_url = 'dashboard/pasien/';
if ($_SESSION['user_role'] == 'admin') {
    $dashboard_url = 'dashboard/admin-dashboard.php';
} else if ($_SESSION['user_role'] == 'dokter') {
    $dashboard_url = 'dashboard/dokter/';
}

// Fetch keranjang items untuk total yang dinamis
$q_cart = mysqli_query($koneksi, "
    SELECT p.harga, k.kuantitas
    FROM keranjang k 
    JOIN produk p ON k.id_produk = p.id 
    WHERE k.id_pengguna = '$user_id'
");
$total_tagihan = 0;
$count_items = 0;
while($row = mysqli_fetch_assoc($q_cart)) {
    $total_tagihan += ($row['harga'] * $row['kuantitas']);
    $count_items++;
}
$biaya_layanan = 2000;
$total_pengiriman = 23000;
if ($count_items == 0) {
    $total_pengiriman = 0;
    $biaya_layanan = 0;
}
$grand_total = $total_tagihan + $total_pengiriman + $biaya_layanan;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - DiVera Medical</title>
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
        
        .payment-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            max-width: 500px;
            margin: 0 auto;
            border: 1px solid #eee;
        }
        .payment-header {
            padding: 20px 25px 10px 25px;
        }
        .payment-tabs {
            display: flex;
            background-color: #f1f3f5;
            margin: 0 20px;
            border-radius: 8px;
            overflow: hidden;
        }
        .payment-tab {
            flex: 1;
            text-align: center;
            padding: 10px 5px;
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
            cursor: pointer;
        }
        .payment-tab.active {
            background-color: white;
            color: #E91E63;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .ewallet-list {
            padding: 10px 20px;
        }
        .ewallet-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: flex-start;
        }
        .ewallet-item:last-child {
            border-bottom: none;
        }
        .ewallet-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 3px;
        }
        .ewallet-warning {
            font-size: 11px;
            color: #dc3545;
            display: flex;
            align-items: center;
        }
        .ewallet-icon {
            width: 80px;
            font-weight: bold;
            font-size: 14px;
        }
        .btn-bayar {
            background-color: #f1f3f5;
            color: #495057;
            font-weight: bold;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
        }
        .btn-bayar.active {
            background-color: #E91E63;
            color: white;
        }
        
        .wallet-option {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
                <?php 
                $foto_profil = isset($_SESSION['user_foto']) && !empty($_SESSION['user_foto']) ? 'asset/img/profil/' . $_SESSION['user_foto'] : 'asset/img/icon-female.png';
                ?>
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
        <div class="step completed">
            <div class="step-circle"><i class="fa-solid fa-check"></i></div> Alamat
        </div>
        <div class="step-line"></div>
        <div class="step active">
            <div class="step-circle">3</div> Pembayaran
        </div>
    </div>

    <!-- Main Content -->
    <div class="container pb-5 mt-4">
        <div class="payment-card mb-5">
            <div class="payment-header">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Tagihan</span>
                    <span class="fw-bold">Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                    <span class="fw-bold fs-5">Pembayaranmu</span>
                    <span class="fw-bold fs-5 text-dark">Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
                </div>

                <!-- Internal Wallets Removed -->
            </div>

            <!-- Payment Tabs -->
            <div class="payment-tabs mb-2 mt-3">
                <div class="payment-tab active" id="tab-ewallet" onclick="switchTab('ewallet')">Uang<br>Elektronik</div>
                <div class="payment-tab" id="tab-va" onclick="switchTab('va')">Virtual<br>Account</div>
                <div class="payment-tab" id="tab-cc" onclick="switchTab('cc')">Kartu<br>Kredit/Debit</div>
            </div>

            <!-- E-Wallet Options -->
            <div class="ewallet-list" id="content-ewallet">
                <label class="ewallet-item w-100" style="cursor: pointer;">
                    <div class="ewallet-icon text-primary">gopay</div>
                    <div class="flex-grow-1">
                        <div class="ewallet-name">gopay</div>
                        <div class="ewallet-warning">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> Sambungkan gopay sebagai metode pembayaran.
                        </div>
                    </div>
                    <input type="radio" name="payment_method" class="form-check-input mt-1">
                </label>

                <label class="ewallet-item w-100" style="cursor: pointer;">
                    <div class="ewallet-icon text-warning" style="color:#ee4d2d !important;">Shopee</div>
                    <div class="flex-grow-1">
                        <div class="ewallet-name">ShopeePay</div>
                        <div class="ewallet-warning">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> Sambungkan ShopeePay sebagai metode pembayaran.
                        </div>
                    </div>
                    <input type="radio" name="payment_method" class="form-check-input mt-1">
                </label>

                <label class="ewallet-item w-100" style="cursor: pointer;">
                    <div class="ewallet-icon text-primary fw-bold" style="font-style: italic;">DANA</div>
                    <div class="flex-grow-1">
                        <div class="ewallet-name">DANA</div>
                        <div class="ewallet-warning">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> Sambungkan DANA sebagai metode pembayaran.
                        </div>
                    </div>
                    <input type="radio" name="payment_method" class="form-check-input mt-1">
                </label>

                <label class="ewallet-item w-100" style="cursor: pointer;">
                    <div class="ewallet-icon" style="color: #4c3494;">OVO</div>
                    <div class="flex-grow-1">
                        <div class="ewallet-name">OVO</div>
                        <div class="ewallet-warning">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> Sambungkan OVO sebagai metode pembayaran.
                        </div>
                    </div>
                    <input type="radio" name="payment_method" class="form-check-input mt-1">
                </label>
                
                <label class="ewallet-item w-100" style="cursor: pointer;">
                    <div class="ewallet-icon text-danger">LinkAja</div>
                    <div class="flex-grow-1">
                        <div class="ewallet-name">LinkAja</div>
                        <div class="ewallet-warning">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> Sambungkan LinkAja sebagai metode pembayaran.
                        </div>
                    </div>
                    <input type="radio" name="payment_method" class="form-check-input mt-1">
                </label>

                <label class="ewallet-item w-100" style="cursor: pointer;">
                    <div class="ewallet-icon text-primary">AstraPay</div>
                    <div class="flex-grow-1">
                        <div class="ewallet-name">AstraPay</div>
                    </div>
                    <input type="radio" name="payment_method" class="form-check-input mt-1">
                </label>
            </div>

            <!-- Virtual Account Options -->
            <div class="ewallet-list" id="content-va" style="display: none;">
                <label class="ewallet-item w-100" style="cursor: pointer;">
                    <div class="ewallet-icon text-primary fw-bold" style="font-size: 16px;">BCA</div>
                    <div class="flex-grow-1">
                        <div class="ewallet-name" style="font-weight: normal; font-size: 14px;">BCA Virtual Account</div>
                    </div>
                    <input type="radio" name="payment_method" class="form-check-input mt-1">
                </label>
                <label class="ewallet-item w-100" style="cursor: pointer;">
                    <div class="ewallet-icon text-warning fw-bold" style="font-size: 16px;">Mandiri</div>
                    <div class="flex-grow-1">
                        <div class="ewallet-name" style="font-weight: normal; font-size: 14px;">Mandiri Virtual Account</div>
                    </div>
                    <input type="radio" name="payment_method" class="form-check-input mt-1">
                </label>
                <label class="ewallet-item w-100" style="cursor: pointer;">
                    <div class="ewallet-icon text-primary fw-bold" style="font-size: 16px;">BRI</div>
                    <div class="flex-grow-1">
                        <div class="ewallet-name" style="font-weight: normal; font-size: 14px;">BRI Virtual Account</div>
                    </div>
                    <input type="radio" name="payment_method" class="form-check-input mt-1">
                </label>
                <label class="ewallet-item w-100" style="cursor: pointer;">
                    <div class="ewallet-icon fw-bold" style="color: #f37021; font-size: 16px;">BNI</div>
                    <div class="flex-grow-1">
                        <div class="ewallet-name" style="font-weight: normal; font-size: 14px;">BNI Virtual Account</div>
                    </div>
                    <input type="radio" name="payment_method" class="form-check-input mt-1">
                </label>
                <label class="ewallet-item w-100" style="cursor: pointer;">
                    <div class="ewallet-icon text-success fw-bold" style="font-size: 13px;">Permata</div>
                    <div class="flex-grow-1">
                        <div class="ewallet-name" style="font-weight: normal; font-size: 14px;">Permata Virtual Account</div>
                    </div>
                    <input type="radio" name="payment_method" class="form-check-input mt-1">
                </label>
            </div>

            <!-- Credit Card Options -->
            <div class="ewallet-list py-3" id="content-cc" style="display: none;">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nomor Kartu</label>
                    <input type="text" class="form-control" placeholder="0000 0000 0000 0000">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Masa Berlaku</label>
                        <input type="text" class="form-control" placeholder="MM/YY">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">CVV</label>
                        <input type="password" class="form-control" placeholder="123">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Nama di Kartu</label>
                    <input type="text" class="form-control" placeholder="Nama Lengkap">
                </div>
                <div class="form-check mt-3">
                    <input type="checkbox" class="form-check-input" id="saveCard" name="payment_method" checked>
                    <label class="form-check-label small" for="saveCard">Gunakan kartu ini untuk pembayaran</label>
                </div>
            </div>

            <div class="p-3 bg-white" style="position: sticky; bottom: 0; box-shadow: 0 -2px 10px rgba(0,0,0,0.05);">
                <button class="btn btn-bayar active" onclick="alert('Terima kasih. Pesanan Anda sedang diproses!')">Bayar</button>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            // Update Tab Styling
            document.getElementById('tab-ewallet').classList.remove('active', 'text-primary-custom');
            document.getElementById('tab-va').classList.remove('active', 'text-primary-custom');
            document.getElementById('tab-cc').classList.remove('active', 'text-primary-custom');
            
            document.getElementById('tab-' + tabId).classList.add('active', 'text-primary-custom');

            // Update Content
            document.getElementById('content-ewallet').style.display = 'none';
            document.getElementById('content-va').style.display = 'none';
            document.getElementById('content-cc').style.display = 'none';

            document.getElementById('content-' + tabId).style.display = 'block';
        }

        // Add event listeners to radio buttons to make Pay button active
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const btn = document.querySelector('.btn-bayar');
                btn.classList.add('active');
            });
        });
    </script>
</body>
</html>

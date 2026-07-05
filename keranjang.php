<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash'] = ['status' => 'error', 'message' => 'Anda harus login untuk mengakses keranjang.'];
    header("Location: login");
    exit;
}

$user_id = $_SESSION['user_id'];
$is_logged_in = true;
$user_nama = $_SESSION['user_nama'];
$initial = strtoupper(substr($user_nama, 0, 1));
$dashboard_url = 'dashboard/pasien/';
if ($_SESSION['user_role'] == 'admin') {
    $dashboard_url = 'dashboard/admin-dashboard.php';
} else if ($_SESSION['user_role'] == 'dokter') {
    $dashboard_url = 'dashboard/dokter/';
}

// Fetch keranjang items
$q_cart = mysqli_query($koneksi, "
    SELECT k.*, p.nama_produk, p.harga, p.url_gambar 
    FROM keranjang k 
    JOIN produk p ON k.id_produk = p.id 
    WHERE k.id_pengguna = '$user_id'
");
$cart_items = [];
$total_tagihan = 0;
while($row = mysqli_fetch_assoc($q_cart)) {
    $cart_items[] = $row;
    $total_tagihan += ($row['harga'] * $row['kuantitas']);
}
$biaya_layanan = 2000;
$total_pengiriman = 23000;
if (count($cart_items) == 0) {
    $total_pengiriman = 0;
    $biaya_layanan = 0;
}
$grand_total = $total_tagihan + $total_pengiriman + $biaya_layanan;

// Fetch 4 produk dengan stok paling sedikit untuk rekomendasi (Sering Dibeli Berbarengan)
$q_rekomendasi = mysqli_query($koneksi, "
    SELECT id, nama_produk, harga, url_gambar 
    FROM produk 
    WHERE stok > 0 
    ORDER BY stok ASC 
    LIMIT 4
");
$rekomendasi_items = [];
while($row = mysqli_fetch_assoc($q_rekomendasi)) {
    $rekomendasi_items[] = $row;
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
        <div class="step active">
            <div class="step-circle">1</div> Keranjang
        </div>
        <div class="step-line"></div>
        <div class="step text-muted">
            <div class="step-circle">2</div> Alamat
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
                <?php if (count($cart_items) > 0): ?>
                    <?php foreach ($cart_items as $item): ?>
                    <div class="cart-card d-flex align-items-center mb-3">
                        <img src="asset/img/<?= !empty($item['url_gambar']) && file_exists('asset/img/produk/'.$item['url_gambar']) ? 'produk/'.$item['url_gambar'] : '600x400.jpg' ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>" width="80" height="80" class="rounded border me-3" style="object-fit: cover;">
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1"><?= htmlspecialchars($item['nama_produk']) ?></h6>
                            <p class="text-muted small mb-0">Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold mb-2">Rp <?= number_format($item['harga'] * $item['kuantitas'], 0, ',', '.') ?></div>
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <form action="config/function_product.php" method="POST" class="d-inline">
                                    <input type="hidden" name="id_keranjang" value="<?= $item['id'] ?>">
                                    <button type="submit" name="btn_hapus_keranjang" class="btn btn-sm btn-link p-0 border-0 text-muted">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                                <form action="config/function_product.php" method="POST" class="d-inline">
                                    <input type="hidden" name="id_keranjang" value="<?= $item['id'] ?>">
                                    <input type="hidden" name="action" value="minus">
                                    <button type="submit" name="btn_update_keranjang" class="qty-btn ms-2 border-0" style="width:25px;height:25px;">-</button>
                                </form>
                                <span class="fw-bold px-1"><?= $item['kuantitas'] ?></span>
                                <form action="config/function_product.php" method="POST" class="d-inline">
                                    <input type="hidden" name="id_keranjang" value="<?= $item['id'] ?>">
                                    <input type="hidden" name="action" value="plus">
                                    <button type="submit" name="btn_update_keranjang" class="qty-btn border-0" style="width:25px;height:25px;">+</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-light text-center py-5 border">
                        <i class="fa-solid fa-cart-arrow-down fa-3x text-muted mb-3"></i>
                        <h6 class="fw-bold text-muted">Keranjang masih kosong</h6>
                        <a href="obat-vitamin" class="btn btn-primary-custom btn-sm mt-2 rounded-pill px-4">Belanja Sekarang</a>
                    </div>
                <?php endif; ?>

                <!-- Sering Dibeli Berbarengan -->
                <?php if(count($rekomendasi_items) > 0): ?>
                <h6 class="fw-bold mt-4 mb-3">Sering Dibeli Berbarengan</h6>
                <div class="d-flex gap-3 overflow-auto pb-3">
                    <?php foreach($rekomendasi_items as $item): ?>
                    <div class="border rounded p-2 text-center" style="min-width: 140px; background: white;">
                        <img src="<?= htmlspecialchars(!empty($item['url_gambar']) ? 'asset/img/produk/' . $item['url_gambar'] : 'asset/img/female-doctor.png') ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>" width="60" height="60" class="mb-2" style="object-fit: cover; border-radius: 8px;">
                        <div class="small fw-bold text-truncate" style="font-size: 11px;" title="<?= htmlspecialchars($item['nama_produk']) ?>"><?= htmlspecialchars($item['nama_produk']) ?></div>
                        <div class="small text-muted mb-2" style="font-size: 11px;">Rp <?= number_format($item['harga'], 0, ',', '.') ?></div>
                        <form action="config/function_product.php" method="POST">
                            <input type="hidden" name="id_produk" value="<?= $item['id'] ?>">
                            <button type="submit" name="btn_add_keranjang" class="btn btn-primary-custom btn-sm w-100 p-0" style="height: 25px;">+</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
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
                        <span class="text-muted">Keranjang (<?= count($cart_items) ?> Item)</span>
                        <span>Rp <?= number_format($total_tagihan, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Total Pengiriman</span>
                        <span>Rp <?= number_format($total_pengiriman, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small">
                        <span class="text-muted">Biaya Layanan</span>
                        <span>Rp <?= number_format($biaya_layanan, 0, ',', '.') ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Total Tagihan</span>
                        <span class="fw-bold fs-5 text-primary-custom">Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
                    </div>

                    <button class="btn btn-primary-custom w-100 py-2 fw-bold <?= count($cart_items) == 0 ? 'disabled' : '' ?>" onclick="window.location.href='alamat.php'">Berikutnya</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

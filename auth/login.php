<?php

require_once '../config/config.php';

if (!empty($sesi_id)) {
    header('Location: ../dashboard/admin');
    exit;
}

$usernameLogin = isset($_GET['username']) ? $_GET['username'] : '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="<?= DESKRIPSI_WEB ?>">
    <meta name="robots" content="noindex, nofollow">

    <title>Login Admin | <?= NAMA_WEB ?></title>

    <link rel="shortcut icon" href="../assets/logo.png">
    <link rel="stylesheet" href="../dashboard/assets/compiled/css/app.css">
    <link rel="stylesheet" href="../dashboard/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="../dashboard/assets/compiled/css/auth.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="../assets/style.css">

</head>

<body>
    <script src="../dashboard/assets/static/js/initTheme.js"></script>

    <div class="auth-shell">

        <div class="brand-top">
            <img src="../assets/logo.png">
            <div>
                <div class="title"><?= NAMA_WEB ?></div>
                <div class="sub"><?= NAMA_KAMPUS ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="auth-title">Login Admin</h2>
                <p class="auth-subtitle">
                    Sistem Informasi Peringkat Kafe Kopi Bandar Lampung
                </p>
            </div>

            <div class="card-body">
                <form action="../functions/function_auth.php" method="post" autocomplete="off">

                    <div class="form-group position-relative has-icon-left mb-3">
                        <label>Username</label>
                        <div class="position-relative">
                            <input type="text" name="username" class="form-control"
                                placeholder="Username admin"
                                value="<?= $usernameLogin ?>" required>
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group position-relative has-icon-left mb-3">
                        <label>Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" class="form-control"
                                placeholder="Password admin"
                                required>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="btn_login" class="btn btn-primary w-100">
                        Masuk ke Dashboard
                    </button>

                    <!-- Tombol Beranda (sesuai permintaan) -->
                    <a href="../index" class="btn btn-secondary w-100 mt-3">
                        Beranda
                    </a>
                </form>

                <div class="text-center mt-3">
                    <small class="text-muted">
                        <?= MATKUL ?><br>
                        <?= NAMA_LENGKAP ?> · <a href="<?= URL_IG ?>" target="_blank"><?= IG ?></a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Parsley -->
    <script src="https://cdn.jsdelivr.net/npm/parsleyjs@2.9.2/dist/parsley.min.js"></script>
    <script src="assets/static/js/pages/parsley.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php require_once '../config/sweetalert.php'; ?>

</body>

</html>
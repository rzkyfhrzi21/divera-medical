<?php
session_start();
require_once 'koneksi.php';

/* =====================================================
   FUNCTION AUTHENTIKASI & PROFIL
   -----------------------------------------------------
   Fungsi utama file ini:
   - Mengelola proses login (validasi akun, set session)
   - Mengelola registrasi akun baru (enkripsi password)
   - Mengelola update profil pengguna
   - Mengelola logout (hapus session)

   Semua notifikasi disimpan ke $_SESSION['flash']
   agar tidak tampil di URL (clean URL).
===================================================== */

/* ======================================================
   HELPER: SET FLASH MESSAGE
   Menyimpan status dan pesan ke session untuk
   ditampilkan oleh SweetAlert2 di halaman tujuan.
====================================================== */
function setFlash($status, $message) {
    $_SESSION['flash'] = [
        'status'  => $status,
        'message' => $message
    ];
}

/* ======================================================
   REGISTER PENGGUNA
   1. Ambil data dari form
   2. Validasi konfirmasi password
   3. Cek apakah email sudah terdaftar
   4. Hash password lalu insert ke database
====================================================== */
if (isset($_POST['btn_register'])) {

    // Ambil & sanitasi input dari form registrasi
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $telepon  = mysqli_real_escape_string($koneksi, $_POST['telepon']);
    $role     = isset($_POST['role']) ? $_POST['role'] : 'pasien';
    $password_raw     = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Validasi: password dan konfirmasi harus sama
    if ($password_raw !== $password_confirm) {
        setFlash('error', 'Password konfirmasi tidak cocok!');
        header("Location: ../register");
        exit;
    }

    // Hash password menggunakan bcrypt
    $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);

    // Cek apakah email sudah dipakai pengguna lain
    $cek = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        setFlash('error', 'Email sudah terdaftar!');
        header("Location: ../register");
        exit;
    }

    // Insert data pengguna baru ke database
    $insert = mysqli_query($koneksi, "
        INSERT INTO pengguna (nama, email, password, role, telpon)
        VALUES ('$nama', '$email', '$password_hash', '$role', '$telepon')
    ");

    if ($insert) {
        // Registrasi berhasil, arahkan ke login dengan flash sukses
        setFlash('success', 'Registrasi berhasil! Silakan login.');
        header("Location: ../login");
    } else {
        // Query gagal
        setFlash('error', 'Gagal melakukan registrasi.');
        header("Location: ../register");
    }
    exit;
}

/* ======================================================
   LOGIN PENGGUNA
   1. Ambil email dan password dari form
   2. Cek email di database
   3. Verifikasi hash password
   4. Set session dan arahkan ke dashboard sesuai role
====================================================== */
if (isset($_POST['btn_login'])) {

    // Sanitasi input email
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // Cari pengguna berdasarkan email
    $cek = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE email='$email'");

    if (mysqli_num_rows($cek) > 0) {
        $user = mysqli_fetch_assoc($cek);

        // Verifikasi password yang di-hash
        if (password_verify($password, $user['password'])) {
            // Set session login
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['user_role'] = $user['role'];

            // Tentukan URL dashboard sesuai role pengguna
            $url_dashboard = 'dashboard/pasien/'; // Default: pasien
            if ($user['role'] == 'dokter') {
                $url_dashboard = 'dashboard/dokter/';
            }

            // Simpan URL dashboard ke session agar bisa dipakai di SweetAlert
            setFlash('login_success', 'Login berhasil, selamat datang!');
            $_SESSION['redirect_url'] = $url_dashboard;
            header("Location: ../login");
            exit;
        } else {
            // Password tidak cocok
            setFlash('error', 'Password salah!');
            header("Location: ../login");
            exit;
        }
    } else {
        // Email tidak ditemukan di database
        setFlash('error', 'Email tidak ditemukan!');
        header("Location: ../login");
        exit;
    }
}

/* ======================================================
   UPDATE PROFIL PENGGUNA
   1. Pastikan user sudah login
   2. Ambil data dari form
   3. Validasi email agar tidak bentrok
   4. Update ke database
====================================================== */
if (isset($_POST['btn_update_profil'])) {

    // Cek apakah user sudah login
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login");
        exit;
    }

    $id_pengguna = $_SESSION['user_id'];
    $nama    = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email   = mysqli_real_escape_string($koneksi, $_POST['email']);
    $telepon = mysqli_real_escape_string($koneksi, $_POST['telepon']);

    // Cek email bentrok dengan pengguna lain
    $cek = mysqli_query($koneksi, "SELECT id FROM pengguna WHERE email = '$email' AND id != '$id_pengguna'");
    if (mysqli_num_rows($cek) > 0) {
        setFlash('error', 'Email sudah dipakai pengguna lain!');
        header("Location: ../dashboard/admin-dashboard");
        exit;
    }

    // Jalankan query update data profil
    $update = mysqli_query($koneksi, "
        UPDATE pengguna SET
        nama='$nama',
        email='$email',
        telpon='$telepon'
        WHERE id='$id_pengguna'
    ");

    if ($update) {
        // Update session nama agar langsung terlihat di navbar
        $_SESSION['user_nama'] = $nama;
        setFlash('success', 'Profil berhasil diupdate!');
    } else {
        setFlash('error', 'Gagal mengupdate profil.');
    }
    header("Location: ../dashboard/admin-dashboard");
    exit;
}

/* ======================================================
   LOGOUT PENGGUNA
   1. Hapus semua data session
   2. Arahkan ke halaman login dengan flash message
====================================================== */
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    // Simpan flash message sebelum destroy session
    $flash = ['status' => 'success', 'message' => 'Berhasil logout!'];

    // Hapus semua data session
    session_unset();
    session_destroy();

    // Buat session baru khusus untuk flash message
    session_start();
    $_SESSION['flash'] = $flash;

    // Arahkan ke halaman login
    header("Location: ../login");
    exit;
}
?>

<?php
/* =====================================================
   FUNCTION ADMIN - SIP KAFE
   -----------------------------------------------------
   Fungsi utama file ini:
   - Mengelola update profil admin (nama, username, email, no HP)
   - Mengelola update password admin
   - Hanya bisa diakses oleh admin yang sudah login
===================================================== */

require_once '../config/config.php'; // (1) Memanggil konfigurasi & koneksi database

// =====================================================
// CEK SESSION LOGIN
// -----------------------------------------------------
// Jika session admin tidak ada, berarti user belum login
// Akses langsung ke file ini akan diblokir
// =====================================================
if (!isset($_SESSION['sesi_id'])) {
    header("Location: ../auth/login?status=warning&action=auth&ket=belumlogin");
    exit;
}

// Ambil ID admin dari session (dipakai di query UPDATE)
$id_admin = $_SESSION['sesi_id'];

// =====================================================
// VALIDASI REQUEST
// -----------------------------------------------------
// File ini hanya boleh diakses melalui metode POST
// (mencegah akses langsung via URL / refresh halaman)
// =====================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../dashboard/admin");
    exit;
}

/* =====================================================
   UPDATE PROFIL ADMIN
   -----------------------------------------------------
   Diproses jika tombol "Update Profil" diklik
===================================================== */
if (isset($_POST['btn_update_profile'])) {

    // Ambil & sanitasi input dari form profil
    // Sanitasi untuk mengurangi risiko SQL Injection
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username     = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email        = mysqli_real_escape_string($koneksi, $_POST['email']);
    $no_hp        = mysqli_real_escape_string($koneksi, $_POST['no_hp']);

    /* -------------------------------------------------
       VALIDASI USERNAME
       -------------------------------------------------
       Mengecek apakah username baru sudah digunakan
       oleh admin lain (kecuali dirinya sendiri)
    ------------------------------------------------- */
    $cek = mysqli_query(
        $koneksi,
        "SELECT id_admin FROM admin 
         WHERE username = '$username' 
         AND id_admin != '$id_admin'"
    );

    // Jika username sudah dipakai admin lain
    if (mysqli_num_rows($cek) > 0) {
        header("Location: ../dashboard/admin?page=Profil&status=warning&action=update_profile&ket=username_exist");
        exit;
    }

    /* -------------------------------------------------
       UPDATE DATA PROFIL ADMIN
       -------------------------------------------------
       Mengubah data admin berdasarkan ID admin login
    ------------------------------------------------- */
    $update = mysqli_query(
        $koneksi,
        "UPDATE admin SET
            nama_lengkap = '$nama_lengkap',
            username     = '$username',
            email        = '$email',
            no_hp        = '$no_hp'
         WHERE id_admin = '$id_admin'"
    );

    // Jika query berhasil
    if ($update) {
        header("Location: ../dashboard/admin?page=Profil&status=success&action=update_profile&ket=success");
        exit;
    }
    // Jika query gagal
    else {
        header("Location: ../dashboard/admin?page=Profil&status=error&action=update_profile&ket=query_failed");
        exit;
    }
}

/* =====================================================
   UPDATE PASSWORD ADMIN
   -----------------------------------------------------
   Diproses jika tombol "Update Password" diklik
===================================================== */
if (isset($_POST['btn_update_password'])) {

    // Ambil input password baru & konfirmasi
    $password_baru       = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    /* -------------------------------------------------
       VALIDASI INPUT PASSWORD
       ------------------------------------------------- */

    // Jika kedua field kosong → tidak boleh lanjut
    if (empty($password_baru) && empty($konfirmasi_password)) {
        header("Location: ../dashboard/admin?page=Profil&status=warning&action=password&ket=empty");
        exit;
    }

    // Jika password dan konfirmasi tidak sama
    if ($password_baru !== $konfirmasi_password) {
        header("Location: ../dashboard/admin?page=Profil&status=warning&action=password&ket=notmatch");
        exit;
    }

    /* -------------------------------------------------
       HASH PASSWORD
       -------------------------------------------------
       Menggunakan MD5 untuk menyesuaikan sistem lama
       (catatan: untuk sistem baru disarankan password_hash)
    ------------------------------------------------- */
    $password_md5 = md5($password_baru);

    // Update password admin
    $update = mysqli_query(
        $koneksi,
        "UPDATE admin SET password = '$password_md5'
         WHERE id_admin = '$id_admin'"
    );

    // Jika update berhasil
    if ($update) {
        header("Location: ../dashboard/admin?page=Profil&status=success&action=password&ket=success");
        exit;
    }
    // Jika update gagal
    else {
        header("Location: ../dashboard/admin?page=Profil&status=error&action=password&ket=query_failed");
        exit;
    }
}

/* =====================================================
   RINGKASAN ALGORITMA (1–2–3)
   -----------------------------------------------------
   UPDATE PROFIL:
   1) Ambil data form + validasi username unik
   2) Jalankan query UPDATE berdasarkan id_admin
   3) Redirect sukses atau gagal

   UPDATE PASSWORD:
   1) Validasi input & kecocokan password
   2) Hash password (MD5)
   3) Update password di database & redirect hasil
===================================================== */

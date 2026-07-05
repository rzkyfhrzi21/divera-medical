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
function setFlash($status, $message)
{
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

    // Tanpa hash password
    $password_hash = $password_raw;

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

        // Verifikasi password
        if ($password == $user['password']) {
            // Set session login
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_telpon'] = $user['telpon'];
            $_SESSION['user_foto'] = $user['foto_profil'];

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
if (isset($_POST['btn_update_profil_pengguna'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login");
        exit;
    }

    $id_pengguna = $_SESSION['user_id'];
    $nama    = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email   = mysqli_real_escape_string($koneksi, $_POST['email']);
    $telepon = mysqli_real_escape_string($koneksi, $_POST['telpon']);
    $password_baru = $_POST['password'];
    $foto_lama = isset($_POST['foto_lama']) ? $_POST['foto_lama'] : '';
    $foto_baru = $foto_lama;

    // Handle upload foto profil
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $foto_baru = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto_profil']['tmp_name'], '../asset/img/profil/' . $foto_baru);
            if (!empty($foto_lama) && file_exists('../asset/img/profil/' . $foto_lama)) {
                unlink('../asset/img/profil/' . $foto_lama);
            }
        }
    }

    $cek = mysqli_query($koneksi, "SELECT id FROM pengguna WHERE email = '$email' AND id != '$id_pengguna'");
    if (mysqli_num_rows($cek) > 0) {
        if ($foto_baru != $foto_lama && file_exists('../asset/img/profil/' . $foto_baru)) unlink('../asset/img/profil/' . $foto_baru);
        setFlash('error', 'Email sudah dipakai pengguna lain!');
        header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index'));
        exit;
    }

    $sql = "UPDATE pengguna SET nama='$nama', email='$email', telpon='$telepon', foto_profil='$foto_baru'";
    if (!empty($password_baru)) {
        $sql .= ", password='$password_baru'";
    }
    $sql .= " WHERE id='$id_pengguna'";

    if (mysqli_query($koneksi, $sql)) {
        $_SESSION['user_nama'] = $nama;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_telpon'] = $telepon;
        $_SESSION['user_foto'] = $foto_baru;
        setFlash('success', 'Profil akun berhasil diperbarui!');
    } else {
        setFlash('error', 'Gagal memperbarui profil akun.');
    }
    header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index'));
    exit;
}

if (isset($_POST['btn_update_profil_dokter'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login");
        exit;
    }

    $id_pengguna = $_SESSION['user_id'];
    $spesialisasi = mysqli_real_escape_string($koneksi, $_POST['spesialisasi']);
    $tahun_pengalaman = intval($_POST['tahun_pengalaman']);
    $biaya = floatval($_POST['biaya']);
    $biografi = mysqli_real_escape_string($koneksi, $_POST['biografi']);

    $update = mysqli_query($koneksi, "
        UPDATE dokter SET 
        spesialisasi='$spesialisasi',
        tahun_pengalaman='$tahun_pengalaman',
        biaya='$biaya',
        biografi='$biografi'
        WHERE id_pengguna='$id_pengguna'
    ");

    if ($update) setFlash('success', 'Data profil dokter berhasil diperbarui!');
    else setFlash('error', 'Gagal memperbarui data dokter.');

    header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index'));
    exit;
}

if (isset($_POST['btn_update_profil_pasien'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login");
        exit;
    }

    $id_pengguna = $_SESSION['user_id'];
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $golongan_darah = mysqli_real_escape_string($koneksi, $_POST['golongan_darah']);
    $tinggi_badan = floatval($_POST['tinggi_badan']);
    $berat_badan = floatval($_POST['berat_badan']);

    $update = mysqli_query($koneksi, "
        UPDATE pasien SET 
        tanggal_lahir='$tanggal_lahir',
        jenis_kelamin='$jenis_kelamin',
        golongan_darah='$golongan_darah',
        tinggi_badan='$tinggi_badan',
        berat_badan='$berat_badan'
        WHERE id_pengguna='$id_pengguna'
    ");

    if ($update) setFlash('success', 'Rekam data pasien berhasil diperbarui!');
    else setFlash('error', 'Gagal memperbarui rekam data pasien.');

    header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index'));
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

<?php
/* =====================================================
   FUNCTION AUTH - SIP KAFE
   Proses Login Admin
   -----------------------------------------------------
   Tujuan file:
   - Menerima input username & password dari form login
   - Memvalidasi request + input
   - Mengecek kecocokan data admin di database
   - Jika cocok: set session admin lalu redirect ke dashboard
   - Jika tidak cocok: redirect balik ke halaman login
===================================================== */

require_once '../config/config.php'; // (1) Memanggil file konfigurasi (biasanya berisi koneksi DB: $koneksi)

/* =====================================================
   VALIDASI REQUEST
   -----------------------------------------------------
   Menghindari akses langsung ke file ini tanpa lewat form login.
   Jika tombol submit (btn_login) tidak ada, dianggap request tidak valid.
===================================================== */
if (!isset($_POST['btn_login'])) {
   header("Location: ../auth/login?status=warning&action=auth&ket=invalid_request");
   exit; // Penting: menghentikan eksekusi supaya tidak lanjut menjalankan query
}

/* =====================================================
   AMBIL & SANITASI INPUT
   -----------------------------------------------------
   Mengambil username & password dari form login.
   mysqli_real_escape_string membantu mengurangi risiko SQL injection
   (meskipun idealnya tetap pakai prepared statement).
===================================================== */
$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = mysqli_real_escape_string($koneksi, $_POST['password']);

/* =====================================================
   VALIDASI INPUT KOSONG
   -----------------------------------------------------
   Jika salah satu field kosong, langsung redirect ke login
   dengan status warning (untuk ditampilkan via alert/sweetalert).
===================================================== */
if (empty($username) || empty($password)) {
   header("Location: ../auth/login?status=warning&action=login&ket=empty");
   exit;
}

/* =====================================================
   ENKRIPSI PASSWORD
   -----------------------------------------------------
   Password user dari form di-hash MD5 agar cocok dengan format lama di DB.
   Catatan: MD5 tidak direkomendasikan untuk sistem baru (kurang aman),
   tapi ini dipertahankan karena mengikuti sistem yang sudah berjalan.
===================================================== */
$password_md5 = md5($password);

/* =====================================================
   ALGORITMA LOGIN (ringkas 1-2-3)
   -----------------------------------------------------
   1) Ambil input username & password, lakukan sanitasi + hashing
   2) Query database untuk cari admin yang username+password cocok
   3) Jika ketemu 1 data → set session & redirect sukses, jika tidak → redirect gagal
===================================================== */

/* =====================================================
   QUERY CEK ADMIN
   -----------------------------------------------------
   Mencari data admin yang:
   - username sama persis dengan input
   - password sama persis dengan hash MD5 input
   LIMIT 1 untuk memastikan hanya ambil 1 baris saja (lebih efisien).
===================================================== */
$query = mysqli_query(
   $koneksi,
   "SELECT * FROM admin 
     WHERE username = '$username'
     AND password = '$password_md5'
     LIMIT 1"
);

$jumlah = mysqli_num_rows($query); // Menghitung jumlah data yang cocok (0 = gagal, 1 = sukses)

/* =====================================================
   LOGIN BERHASIL
   -----------------------------------------------------
   Jika ketemu tepat 1 baris data:
   - Ambil data admin dari hasil query
   - Simpan informasi penting admin ke dalam session
   - Redirect ke dashboard admin dengan status sukses
===================================================== */
if ($jumlah === 1) {

   $data = mysqli_fetch_assoc($query); // Mengambil data admin dalam bentuk array asosiatif

   // Simpan session admin (dipakai untuk autentikasi halaman dashboard)
   $_SESSION['sesi_id']       = $data['id_admin'];       // ID unik admin
   $_SESSION['sesi_username'] = $data['username'];       // Username admin
   $_SESSION['sesi_nama']     = $data['nama_lengkap'];   // Nama lengkap admin
   $_SESSION['sesi_email']    = $data['email'];          // Email admin
   $_SESSION['sesi_nohp']     = $data['no_hp'];          // Nomor HP admin

   // Redirect sukses login (biasanya diproses SweetAlert di halaman tujuan)
   header("Location: ../dashboard/admin?page=Dashboard&status=success&action=login&ket=success");
   exit;
}

/* =====================================================
   LOGIN GAGAL (USERNAME / PASSWORD SALAH)
   -----------------------------------------------------
   Jika tidak ada data yang cocok:
   - Kembalikan user ke halaman login
   - Set status error agar UI menampilkan pesan gagal
===================================================== */
header("Location: ../auth/login?status=error&action=login&ket=invalid");
exit;

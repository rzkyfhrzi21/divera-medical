<?php
session_start();
require_once 'koneksi.php';

/* =====================================================
   FUNCTION JANJI TEMU
   -----------------------------------------------------
   Fungsi utama file ini:
   - Membuat janji temu baru (pasien ke dokter)
   - Update status janji temu (menunggu -> dikonfirmasi)
   - Hapus janji temu

   Semua notifikasi disimpan ke $_SESSION['flash']
   agar URL tetap bersih (tidak ada ?status=... di URL).
===================================================== */

/* ======================================================
   HELPER: SET FLASH MESSAGE
====================================================== */
function setFlash($status, $message) {
    $_SESSION['flash'] = [
        'status'  => $status,
        'message' => $message
    ];
}

/* ======================================================
   BUAT JANJI TEMU
   1. Pastikan user sudah login
   2. Ambil/buat ID pasien dari tabel pasien
   3. Insert data janji temu ke database
====================================================== */
if (isset($_POST['btn_add_janji_temu'])) {
    $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../tanya-dokter';

    // Pastikan user sudah login sebagai pasien
    if (!isset($_SESSION['user_id'])) {
        setFlash('error', 'Anda harus login terlebih dahulu.');
        header("Location: ../login");
        exit;
    }

    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] !== 'pasien') {
        setFlash('error', 'Janji temu hanya dapat dibuat dari akun pasien.');
        header("Location: " . $redirect_url);
        exit;
    }

    // Karena user_id di session adalah ID Pengguna,
    // kita perlu ambil ID Pasien dari tabel pasien
    $id_pengguna = $_SESSION['user_id'];
    $cek_pasien = mysqli_query($koneksi, "SELECT id FROM pasien WHERE id_pengguna='$id_pengguna'");

    // Jika belum punya profil pasien, buat otomatis
    if (mysqli_num_rows($cek_pasien) == 0) {
        mysqli_query($koneksi, "INSERT INTO pasien (id_pengguna) VALUES ('$id_pengguna')");
        // Ambil ID pasien yang baru saja dibuat
        $id_pasien = mysqli_insert_id($koneksi);
    } else {
        // Ambil ID pasien yang sudah ada
        $data_pasien = mysqli_fetch_assoc($cek_pasien);
        $id_pasien = $data_pasien['id'];
    }

    // Ambil data janji temu dari form
    $id_dokter     = intval($_POST['id_dokter']);
    $tanggal_janji = mysqli_real_escape_string($koneksi, $_POST['tanggal_janji'] ?? '');
    $gejala        = mysqli_real_escape_string($koneksi, $_POST['gejala'] ?? '');

    if ($id_dokter <= 0 || empty($tanggal_janji) || empty(trim($gejala))) {
        setFlash('error', 'Dokter, tanggal janji, dan gejala wajib diisi.');
        header("Location: " . $redirect_url);
        exit;
    }

    $cek_dokter = mysqli_query($koneksi, "SELECT id FROM dokter WHERE id='$id_dokter'");
    if (!$cek_dokter || mysqli_num_rows($cek_dokter) == 0) {
        setFlash('error', 'Dokter yang dipilih tidak ditemukan.');
        header("Location: " . $redirect_url);
        exit;
    }

    // Insert janji temu baru dengan status default 'menunggu'
    $insert = mysqli_query($koneksi, "
        INSERT INTO janji_temu (id_dokter, id_pasien, tanggal_janji, gejala, status)
        VALUES ('$id_dokter', '$id_pasien', '$tanggal_janji', '$gejala', 'menunggu')
    ");

    if ($insert) {
        setFlash('success', 'Janji temu berhasil dibuat!');
    } else {
        setFlash('error', 'Gagal membuat janji temu!');
    }
    header("Location: " . $redirect_url);
    exit;
}

/* ======================================================
   UPDATE STATUS JANJI TEMU
   1. Ambil ID janji temu
   2. Update status dan catatan dokter
====================================================== */
if (isset($_POST['btn_update_janji_temu'])) {

    $id      = intval($_POST['id_janji_temu']);
    $status  = mysqli_real_escape_string($koneksi, $_POST['status']);
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan']);

    // Update status dan catatan di tabel janji_temu
    $update = mysqli_query($koneksi, "
        UPDATE janji_temu SET
        status='$status',
        catatan='$catatan'
        WHERE id='$id'
    ");

    if ($update) {
        setFlash('success', 'Status janji temu berhasil diperbarui!');
    } else {
        setFlash('error', 'Gagal memperbarui status!');
    }
    // Kembali ke dashboard halaman Janji Temu
    $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index';
    header("Location: " . $redirect_url);
    exit;
}

/* ======================================================
   HAPUS JANJI TEMU
   1. Ambil ID janji temu
   2. Hapus row dari database
====================================================== */
if (isset($_POST['btn_delete_janji_temu'])) {

    $id = intval($_POST['id_janji_temu']);

    // Hapus data janji temu dari database
    $delete = mysqli_query($koneksi, "DELETE FROM janji_temu WHERE id='$id'");

    if ($delete) {
        setFlash('success', 'Janji temu berhasil dihapus!');
    } else {
        setFlash('error', 'Gagal menghapus janji temu!');
    }
    $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index';
    header("Location: " . $redirect_url);
    exit;
}
?>

<?php
session_start();
require_once 'koneksi.php';

/* =====================================================
   FUNCTION PRODUK
   -----------------------------------------------------
   Fungsi utama file ini:
   - Mengelola penambahan produk (obat, vitamin, dll)
   - Mengelola update informasi produk
   - Mengelola penghapusan produk

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
   TAMBAH PRODUK
   1. Ambil data produk dari form
   2. Proses upload gambar (jika ada)
   3. Insert ke database
====================================================== */
if (isset($_POST['btn_add_produk'])) {

    // Ambil & sanitasi input dari form
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $kategori  = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga     = floatval($_POST['harga']);
    $stok      = intval($_POST['stok']);

    // Proses upload gambar produk
    $foto = null;
    if (!empty($_FILES['gambar_produk']['name'])) {
        // Buat nama file unik agar tidak terjadi duplikasi
        $foto = uniqid() . '.png';
        // Pastikan folder '../asset/img/produk/' sudah ada
        move_uploaded_file($_FILES['gambar_produk']['tmp_name'], "../asset/img/produk/$foto");
    }

    // Insert data produk baru ke tabel produk
    $insert = mysqli_query($koneksi, "
        INSERT INTO produk (nama_produk, kategori, deskripsi, harga, stok, url_gambar)
        VALUES ('$nama', '$kategori', '$deskripsi', '$harga', '$stok', '$foto')
    ");

    if ($insert) {
        setFlash('success', 'Produk berhasil ditambahkan!');
    } else {
        setFlash('error', 'Gagal menambah produk!');
    }
    header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../dashboard/dokter/index?page=Data Produk'));
    exit;
}

/* ======================================================
   EDIT PRODUK
   1. Ambil ID produk yang akan diubah
   2. Ganti foto jika diupload baru, hapus foto lama
   3. Update data ke database
====================================================== */
if (isset($_POST['btn_edit_produk'])) {

    $id        = intval($_POST['id_produk']);
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $kategori  = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga     = floatval($_POST['harga']);
    $stok      = intval($_POST['stok']);
    $foto_lama = $_POST['foto_lama'];

    // Gunakan foto lama sebagai default
    $foto = $foto_lama;
    if (!empty($_FILES['gambar_produk']['name'])) {
        // Hapus foto lama dari server jika ada
        if (!empty($foto_lama) && file_exists("../asset/img/produk/$foto_lama")) {
            unlink("../asset/img/produk/$foto_lama");
        }
        // Upload foto baru dengan nama unik
        $foto = uniqid() . '.png';
        move_uploaded_file($_FILES['gambar_produk']['tmp_name'], "../asset/img/produk/$foto");
    }

    // Jalankan query update produk
    $update = mysqli_query($koneksi, "
        UPDATE produk SET
        nama_produk='$nama', kategori='$kategori', deskripsi='$deskripsi',
        harga='$harga', stok='$stok', url_gambar='$foto'
        WHERE id='$id'
    ");

    if ($update) {
        setFlash('success', 'Produk berhasil diubah!');
    } else {
        setFlash('error', 'Gagal mengubah produk!');
    }
    header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../dashboard/dokter/index?page=Data Produk'));
    exit;
}

/* ======================================================
   HAPUS PRODUK
   1. Hapus foto dari folder server
   2. Hapus row data dari database
====================================================== */
if (isset($_POST['btn_delete_produk'])) {

    $id   = intval($_POST['id_produk']);
    $foto = $_POST['foto_lama'];

    // Hapus file gambar dari server jika ada
    if (!empty($foto) && file_exists("../asset/img/produk/$foto")) {
        unlink("../asset/img/produk/$foto");
    }

    // Hapus data produk dari database
    $delete = mysqli_query($koneksi, "DELETE FROM produk WHERE id='$id'");

    if ($delete) {
        setFlash('success', 'Produk berhasil dihapus!');
    } else {
        setFlash('error', 'Gagal menghapus produk!');
    }
    header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../dashboard/dokter/index?page=Data Produk'));
    exit;
}

/* ======================================================
   TAMBAH KE KERANJANG
   1. Cek login
   2. Cek apakah produk sudah ada di keranjang
   3. Jika ada, tambah kuantitas; Jika tidak, insert baru
====================================================== */
if (isset($_POST['btn_add_keranjang'])) {
    if (!isset($_SESSION['user_id'])) {
        setFlash('error', 'Silakan login terlebih dahulu untuk menambah produk ke keranjang.');
        header("Location: ../login");
        exit;
    }

    $id_pengguna = intval($_SESSION['user_id']);
    $id_produk   = intval($_POST['id_produk']);

    // Cek apakah produk sudah ada di keranjang user
    $cek = mysqli_query($koneksi, "SELECT id, kuantitas FROM keranjang WHERE id_pengguna='$id_pengguna' AND id_produk='$id_produk'");
    if (mysqli_num_rows($cek) > 0) {
        $row = mysqli_fetch_assoc($cek);
        $new_qty = $row['kuantitas'] + 1;
        $id_keranjang = $row['id'];
        mysqli_query($koneksi, "UPDATE keranjang SET kuantitas='$new_qty' WHERE id='$id_keranjang'");
    } else {
        mysqli_query($koneksi, "INSERT INTO keranjang (id_pengguna, id_produk, kuantitas) VALUES ('$id_pengguna', '$id_produk', 1)");
    }

    setFlash('success', 'Produk berhasil dimasukkan ke keranjang!');
    // Redirect kembali ke halaman sebelumnya (atau obat-vitamin.php)
    header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../obat-vitamin'));
    exit;
}

/* ======================================================
   UPDATE / HAPUS DARI KERANJANG
====================================================== */
if (isset($_POST['btn_update_keranjang'])) {
    if (isset($_SESSION['user_id'])) {
        $id_keranjang = intval($_POST['id_keranjang']);
        $action = $_POST['action']; // 'plus' atau 'minus'
        
        $cek = mysqli_query($koneksi, "SELECT kuantitas FROM keranjang WHERE id='$id_keranjang' AND id_pengguna='{$_SESSION['user_id']}'");
        if (mysqli_num_rows($cek) > 0) {
            $row = mysqli_fetch_assoc($cek);
            $qty = $row['kuantitas'];
            
            if ($action == 'plus') {
                $qty++;
                mysqli_query($koneksi, "UPDATE keranjang SET kuantitas='$qty' WHERE id='$id_keranjang'");
            } else if ($action == 'minus') {
                if ($qty > 1) {
                    $qty--;
                    mysqli_query($koneksi, "UPDATE keranjang SET kuantitas='$qty' WHERE id='$id_keranjang'");
                } else {
                    mysqli_query($koneksi, "DELETE FROM keranjang WHERE id='$id_keranjang'");
                }
            }
        }
    }
    header("Location: ../keranjang");
    exit;
}

if (isset($_POST['btn_hapus_keranjang'])) {
    if (isset($_SESSION['user_id'])) {
        $id_keranjang = intval($_POST['id_keranjang']);
        mysqli_query($koneksi, "DELETE FROM keranjang WHERE id='$id_keranjang' AND id_pengguna='{$_SESSION['user_id']}'");
    }
    header("Location: ../keranjang");
    exit;
}
?>

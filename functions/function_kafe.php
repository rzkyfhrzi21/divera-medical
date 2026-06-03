<?php
require_once '../config/config.php';
session_start();

/* ======================================================
   FUNGSI FORMAT NAMA KAFE (CAPITAL)
====================================================== */
function formatNamaKafe($nama)
{
    return ucwords(strtolower(trim($nama)));
}

/* ======================================================
   TAMBAH KAFE
   1. Ambil data
   2. Format nama
   3. Upload foto
====================================================== */
if (isset($_POST['btn_add_kafe'])) {

    $nama_kafe = formatNamaKafe($_POST['nama_kafe']);
    $alamat = $_POST['alamat'];
    $hmin = $_POST['harga_terendah'];
    $hmax = $_POST['harga_tertinggi'];

    // upload foto (jika ada)
    $foto = null;
    if (!empty($_FILES['foto_kafe']['name'])) {
        $foto = uniqid() . '.jpg';
        move_uploaded_file($_FILES['foto_kafe']['tmp_name'], "../dashboard/assets/foto_kafe/$foto");
    }

    $insert = mysqli_query($koneksi, "
        INSERT INTO kafe (nama_kafe, alamat, harga_terendah, harga_tertinggi, foto_kafe)
        VALUES ('$nama_kafe','$alamat','$hmin','$hmax','$foto')
    ");

    if ($insert) {
        header("Location: ../dashboard/admin?page=Data Kafe&status=success&action=addkafe&ket=success");
    } else {
        header("Location: ../dashboard/admin?page=Data Kafe&status=error&action=addkafe&ket=query_failed");
    }
    exit;
}

/* ======================================================
   EDIT KAFE
   1. Ambil ID
   2. Format nama
   3. Replace foto jika diubah
====================================================== */
if (isset($_POST['btn_edit_kafe'])) {

    $id = $_POST['id_kafe'];
    $nama_kafe = formatNamaKafe($_POST['nama_kafe']);
    $alamat = $_POST['alamat'];
    $hmin = $_POST['harga_terendah'];
    $hmax = $_POST['harga_tertinggi'];
    $foto = $_POST['foto_lama'];

    if (!empty($_FILES['foto_kafe']['name'])) {
        if (!empty($foto) && file_exists("../dashboard/assets/foto_kafe/$foto")) {
            unlink("../dashboard/assets/foto_kafe/$foto");
        }
        $foto = uniqid() . '.jpg';
        move_uploaded_file($_FILES['foto_kafe']['tmp_name'], "../dashboard/assets/foto_kafe/$foto");
    }

    $update = mysqli_query($koneksi, "
        UPDATE kafe SET 
        nama_kafe='$nama_kafe',
        alamat='$alamat',
        harga_terendah='$hmin',
        harga_tertinggi='$hmax',
        foto_kafe='$foto'
        WHERE id_kafe='$id'
    ");

    if ($update) {
        header("Location: ../dashboard/admin?page=Data Kafe&status=success&action=editkafe&ket=success");
    } else {
        header("Location: ../dashboard/admin?page=Data Kafe&status=error&action=editkafe&ket=query_failed");
    }
    exit;
}

/* ======================================================
   HAPUS KAFE (VALIDASI KUISIONER)
    1. Hapus foto
    2. Hapus data
====================================================== */
if (isset($_POST['btn_delete_kafe'])) {

    $id   = $_POST['id_kafe'];
    $foto = $_POST['foto_kafe'];

    // (1) CEK APAKAH KAFE MASIH PUNYA DATA KUISIONER
    $cek = mysqli_query($koneksi, "
        SELECT COUNT(*) AS total 
        FROM hasil_kuisioner 
        WHERE id_kafe = '$id'
    ");
    $hasil = mysqli_fetch_assoc($cek);

    // (2) JIKA ADA → BATALKAN HAPUS
    if ($hasil['total'] > 0) {
        header("Location: ../dashboard/admin?page=Data Kafe&status=warning&action=deletekafe&ket=has_kuisioner");
        exit;
    }

    // (3) HAPUS FOTO JIKA ADA
    if (!empty($foto) && file_exists("../dashboard/assets/foto_kafe/$foto")) {
        unlink("../dashboard/assets/foto_kafe/$foto");
    }

    // (4) HAPUS DATA KAFE
    $delete = mysqli_query($koneksi, "DELETE FROM kafe WHERE id_kafe='$id'");

    if ($delete) {
        header("Location: ../dashboard/admin?page=Data Kafe&status=success&action=deletekafe&ket=success");
    } else {
        header("Location: ../dashboard/admin?page=Data Kafe&status=error&action=deletekafe&ket=query_failed");
    }
    exit;
}

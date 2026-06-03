<?php
// =====================================================
// function_riwayat_clustering.php (FINAL FIX)
// - Hapus riwayat clustering
// - Download file CSV clustering
// - Mengikuti skema SweetAlert (status, action, ket)
// =====================================================

require_once '../config/config.php';

// =====================================================
// VALIDASI SESSION
// =====================================================
if (!isset($_SESSION['sesi_id'])) {
    header("Location: ../dashboard/login?status=warning&action=auth&ket=belumlogin");
    exit;
}

// =====================================================
// VALIDASI REQUEST
// =====================================================
$aksi = $_GET['aksi'] ?? $_POST['aksi'] ?? null;
$id_cluster = $_GET['id_cluster'] ?? $_POST['id_cluster'] ?? null;

if (!$aksi || !$id_cluster) {
    header("Location: ../dashboard/admin?page=Riwayat Clustering&status=error&action=riwayat_clustering&ket=invalid_request");
    exit;
}

// =====================================================
// =======================
// AKSI: DOWNLOAD CSV
// =======================
if ($aksi === 'download') {

    // =====================
    // LANGKAH 1: VALIDASI ID
    // =====================
    $id_cluster = $_GET['id_cluster'] ?? null;
    if (!$id_cluster) {
        header("Location: ../dashboard/admin?page=Riwayat Clustering&action=riwayat_clustering&status=error&ket=not_found");
        exit;
    }

    // =====================
    // LANGKAH 2: AMBIL DATA RIWAYAT
    // =====================
    $q = mysqli_query($koneksi, "
        SELECT nama_file 
        FROM clustering 
        WHERE id_cluster = '$id_cluster'
    ");

    if (mysqli_num_rows($q) === 0) {
        header("Location: ../dashboard/admin?page=Riwayat Clustering&action=riwayat_clustering&status=error&ket=not_found");
        exit;
    }

    $data = mysqli_fetch_assoc($q);
    $file = $data['nama_file'];
    $path = "../dashboard/assets/file_clustering/" . $file;

    // =====================
    // LANGKAH 3: DOWNLOAD FILE
    // =====================
    if (!file_exists($path)) {
        header("Location: ../dashboard/admin?page=Riwayat Clustering&action=riwayat_clustering&status=error&ket=file_missing");
        exit;
    }

    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=\"$file\"");
    header("Content-Length: " . filesize($path));
    readfile($path);
    exit;
}

// =====================================================
// =======================
// AKSI: DELETE RIWAYAT
// =======================
if ($aksi === 'delete') {

    // =====================
    // LANGKAH 1: VALIDASI ID
    // =====================
    $id_cluster = $_POST['id_cluster'] ?? null;
    if (!$id_cluster) {
        header("Location: ../dashboard/admin?page=Riwayat Clustering&action=riwayat_clustering&status=error&ket=not_found");
        exit;
    }

    // =====================
    // LANGKAH 2: CEK ID TERBARU (TIDAK BOLEH DIHAPUS)
    // =====================
    $cekMax = mysqli_query($koneksi, "SELECT MAX(id_cluster) AS max_id FROM clustering");
    $max = mysqli_fetch_assoc($cekMax)['max_id'];

    if ($id_cluster == $max) {
        header("Location: ../dashboard/admin?page=Riwayat Clustering&action=riwayat_clustering&status=warning&ket=blocked");
        exit;
    }

    // =====================
    // LANGKAH 3: HAPUS DATA TERKAIT
    // =====================

    // Ambil file CSV
    $qFile = mysqli_query($koneksi, "
        SELECT nama_file 
        FROM clustering 
        WHERE id_cluster = '$id_cluster'
    ");

    if (mysqli_num_rows($qFile) === 0) {
        header("Location: ../dashboard/admin?page=Riwayat Clustering&action=riwayat_clustering&status=error&ket=not_found");
        exit;
    }

    $file = mysqli_fetch_assoc($qFile)['nama_file'];
    $path = "../dashboard/assets/file_clustering/" . $file;

    // Hapus hasil clustering (FK manual / logis)
    mysqli_query($koneksi, "
        DELETE FROM hasil_clustering 
        WHERE id_cluster = '$id_cluster'
    ");

    // Hapus riwayat clustering
    mysqli_query($koneksi, "
        DELETE FROM clustering 
        WHERE id_cluster = '$id_cluster'
    ");

    // Hapus file CSV (jika ada)
    if ($file && file_exists($path)) {
        unlink($path);
    }

    header("Location: ../dashboard/admin?page=Riwayat Clustering&action=riwayat_clustering&status=success&ket=deleted");
    exit;
}

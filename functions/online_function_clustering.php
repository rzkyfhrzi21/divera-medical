<?php
// =====================================================
// function_clustering.php (FINAL FIXED VERSION with Cronjob support)
// Controller proses clustering SIP Kafe
// - Gunakan SESSION untuk pesan error (bukan URL)
// - Perbaiki redirect yang terlalu panjang
// - Deteksi environment (localhost vs hosting) untuk jalankan clustering atau hanya upload
// - Hapus data hanya jika clustering sukses
// =====================================================

// Mulai sesi agar bisa simpan pesan flash
session_start();
require_once '../config/config.php';

// Helper redirect dengan session flash message
function redirect_with_message($page, $status, $action, $ket, $msg = '')
{
    $_SESSION['flash_status'] = $status;
    $_SESSION['flash_action'] = $action;
    $_SESSION['flash_ket'] = $ket;
    if (!empty($msg)) {
        $_SESSION['flash_msg'] = $msg;
    }
    // Redirect ke halaman dashboard admin dengan parameter page
    header("Location: ../dashboard/admin?page=$page");
    exit;
}

// ===== TAHAP 0: VALIDASI FORM SUBMIT =====
// Pastikan form dikirim dengan tombol submit (btn_mulai_clustering)
if (!isset($_POST['btn_mulai_clustering'])) {
    redirect_with_message('Mulai Clustering', 'error', 'clustering', 'invalid_request');
}

// ===== Ambil bobot WSM dan validasi total bobot harus 100 =====
$bobot_rasa      = isset($_POST['bobot_rasa']) ? (float)$_POST['bobot_rasa'] : 0;
$bobot_pelayanan = isset($_POST['bobot_pelayanan']) ? (float)$_POST['bobot_pelayanan'] : 0;
$bobot_fasilitas = isset($_POST['bobot_fasilitas']) ? (float)$_POST['bobot_fasilitas'] : 0;
$bobot_suasana   = isset($_POST['bobot_suasana']) ? (float)$_POST['bobot_suasana'] : 0;
$bobot_harga     = isset($_POST['bobot_harga']) ? (float)$_POST['bobot_harga'] : 0;
$bobot_rating    = isset($_POST['bobot_rating']) ? (float)$_POST['bobot_rating'] : 0;

$total_bobot = $bobot_rasa + $bobot_pelayanan + $bobot_fasilitas +
    $bobot_suasana + $bobot_harga + $bobot_rating;
if ($total_bobot != 100) {
    redirect_with_message('Mulai Clustering', 'error', 'clustering', 'invalid_weight_total', 'Total bobot harus 100%');
}

// Konversi bobot ke desimal (0-1)
$w_rasa      = $bobot_rasa / 100;
$w_pelayanan = $bobot_pelayanan / 100;
$w_fasilitas = $bobot_fasilitas / 100;
$w_suasana   = $bobot_suasana / 100;
$w_harga     = $bobot_harga / 100;
$w_rating    = $bobot_rating / 100;

// ===== TAHAP 1: VALIDASI FILE UPLOAD =====
if (!isset($_FILES['dataset_csv']) || $_FILES['dataset_csv']['error'] !== 0) {
    redirect_with_message('Mulai Clustering', 'warning', 'clustering', 'file_missing');
}

// Tentukan folder upload dan buat jika belum ada
$uploadDir = '../dashboard/assets/file_clustering/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$originalName = basename($_FILES['dataset_csv']['name']);
$targetPath   = $uploadDir . $originalName;

// Upload file CSV ke server
if (!move_uploaded_file($_FILES['dataset_csv']['tmp_name'], $targetPath)) {
    redirect_with_message('Mulai Clustering', 'error', 'clustering', 'upload_failed');
}

// Rename file sesuai standar sistem (timestamp)
$finalFileName = date('Y-m-d_H-i-s') . '_Clustering_SIP_Kafe_Balam.csv';
$finalPath = $uploadDir . $finalFileName;
rename($targetPath, $finalPath);
$targetPath = $finalPath;

// ===== TAHAP 2: CEK LOKASI (LOCALHOST ATAU HOSTING) =====
$host = $_SERVER['HTTP_HOST'] ?? '';
$remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';

$isLocalhost = in_array($remoteAddr, ['127.0.0.1', '::1']) ||
    $host === 'localhost' ||
    strpos($host, 'localhost') !== false ||
    strpos($host, '127.0.0.1') !== false;

// ===== JALANKAN SELURUH PROSES JIKA DI LOCALHOST =====
if ($isLocalhost) {

    // Path PHP CLI pada development lokal (sesuaikan jika beda)
    $phpPath    = "C:\\xampp\\php\\php.exe";

    // Path script kmeans.php
    $scriptPath = realpath('../ml/kmeans.php');
    $csvPath    = realpath($targetPath);

    if (!$scriptPath || !$csvPath) {
        redirect_with_message('Mulai Clustering', 'error', 'clustering', 'file_missing_or_wrong_path');
    }

    // Jalankan clustering lewat CLI PHP
    $command = "\"$phpPath\" \"$scriptPath\" \"$csvPath\" 2>&1";
    exec($command, $output, $status);

    $jsonOutput = implode("\n", $output);
    $result = json_decode($jsonOutput, true);

    // Jika exec gagal atau output tidak valid, redirect error dan simpan pesan
    if ($status !== 0 || !isset($result['rows']) || is_null($result)) {
        $_SESSION['flash_msg'] = "Kmeans error atau output tidak valid: " . ($jsonOutput ?: 'Tidak ada output');
        redirect_with_message('Mulai Clustering', 'error', 'clustering', 'kmeans_error');
    }

    // Jika sukses: hapus data lama hasil_clustering & hasil_kuisioner
    mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS=0");
    mysqli_query($koneksi, "TRUNCATE TABLE hasil_clustering");
    mysqli_query($koneksi, "TRUNCATE TABLE hasil_kuisioner");
    mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS=1");

    // Import ulang data kuisioner dari CSV dan hitung nilai WSM
    $handle = fopen($targetPath, 'r');
    fgetcsv($handle, 1000, $delimiter ?? ','); // skip header
    $jumlahData = 0;

    while (($row = fgetcsv($handle, 1000, $delimiter ?? ',')) !== false) {
        $nama_kafe = trim($row[0]);
        $rasa      = (float)($row[1] ?? 0);
        $pelayanan = (float)($row[2] ?? 0);
        $fasilitas = (float)($row[3] ?? 0);
        $suasana   = (float)($row[4] ?? 0);
        $harga     = (float)($row[5] ?? 0);
        $rating    = (float)($row[6] ?? 0);

        $nilai_wsm = round(
            $w_rasa * $rasa +
                $w_pelayanan * $pelayanan +
                $w_fasilitas * $fasilitas +
                $w_suasana * $suasana +
                $w_harga * $harga +
                $w_rating * $rating,
            2
        );

        $nama_kafe_db = mysqli_real_escape_string($koneksi, $nama_kafe);

        $cek = mysqli_query($koneksi, "SELECT id_kafe FROM kafe WHERE LOWER(nama_kafe)=LOWER('$nama_kafe_db')");
        if (mysqli_num_rows($cek) > 0) {
            $data = mysqli_fetch_assoc($cek);
            $id_kafe = $data['id_kafe'];
        } else {
            mysqli_query($koneksi, "INSERT INTO kafe (nama_kafe, alamat, harga_terendah, harga_tertinggi, foto_kafe) VALUES ('" . mysqli_real_escape_string($koneksi, ucwords(strtolower($nama_kafe))) . "','Belum diisi',1,1,'default.jpg')");
            $id_kafe = mysqli_insert_id($koneksi);
        }

        mysqli_query($koneksi, "INSERT INTO hasil_kuisioner (id_kafe, rasa_kopi, pelayanan, fasilitas, suasana, harga, rating, nilai_wsm) VALUES ($id_kafe, $rasa, $pelayanan, $fasilitas, $suasana, $harga, $rating, $nilai_wsm)");

        $jumlahData++;
    }
    fclose($handle);

    // Insert record clustering baru
    $ins = mysqli_query($koneksi, "INSERT INTO clustering (nama_file, jumlah_cluster, jumlah_data, waktu_clustering) VALUES ('" . mysqli_real_escape_string($koneksi, $finalFileName) . "', 3, $jumlahData, NOW())");
    if (!$ins) {
        redirect_with_message('Mulai Clustering', 'error', 'clustering', 'insert_clustering_failed');
    }
    $idClusterBaru = mysqli_insert_id($koneksi);

    // Simpan hasil clustering ke tabel
    foreach ($result['rows'] as $row) {
        $nama_kafe_raw = trim($row['nama_kafe']);
        $nama_kafe_db  = mysqli_real_escape_string($koneksi, $nama_kafe_raw);

        $q = mysqli_query($koneksi, "SELECT id_kafe FROM kafe WHERE LOWER(TRIM(nama_kafe))=LOWER('$nama_kafe_db') LIMIT 1");
        $d = mysqli_fetch_assoc($q);

        if (!$d) {
            mysqli_query($koneksi, "INSERT INTO kafe (nama_kafe, alamat, harga_terendah, harga_tertinggi, foto_kafe) VALUES ('" . mysqli_real_escape_string($koneksi, ucwords(strtolower($nama_kafe_raw))) . "', 'Belum diisi', 1, 1, 'default.jpg')");
            $id_kafe = mysqli_insert_id($koneksi);
        } else {
            $id_kafe = (int)$d['id_kafe'];
        }

        $q_rating = mysqli_query($koneksi, "SELECT ROUND(AVG(rating), 2) as avg_rating FROM hasil_kuisioner WHERE id_kafe = $id_kafe");
        $rating_data = mysqli_fetch_assoc($q_rating);
        $rating_akhir = $rating_data['avg_rating'] ?? 0;

        mysqli_query($koneksi, "INSERT INTO hasil_clustering (id_cluster, id_kafe, cluster, jarak_centroid, peringkat_cluster, rating_akhir) VALUES ($idClusterBaru, $id_kafe, {$row['cluster']}, {$row['jarak_centroid']}, {$row['peringkat_cluster']}, $rating_akhir)");
    }

    // Log aktivitas admin jika ada sesi
    if (isset($_SESSION['sesi_id'])) {
        $log_message = "Clustering berhasil | File: $finalFileName | Data: $jumlahData | K=3 | Bobot: R=$bobot_rasa%, P=$bobot_pelayanan%, F=$bobot_fasilitas%, S=$bobot_suasana%, H=$bobot_harga%, RT=$bobot_rating%";
        mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_admin, aktivitas, waktu) VALUES ({$_SESSION['sesi_id']}, '" . mysqli_real_escape_string($koneksi, $log_message) . "', NOW())");
    }

    // Redirect sukses ke hasil clustering
    redirect_with_message('Hasil Clustering', 'success', 'clustering', 'process_completed');
} else {
    // Jika hosting atau non-localhost, hanya upload file dan berikan pesan,
    // clustering akan dijalankan oleh cronjob terpisah.
    $_SESSION['flash_msg'] = 'File berhasil diupload. Proses clustering akan diproses secara otomatis oleh sistem.';
    redirect_with_message('Mulai Clustering', 'success', 'clustering', 'upload_success');
}
// END OF FILE
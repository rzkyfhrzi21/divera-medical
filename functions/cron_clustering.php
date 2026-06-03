<?php
// =====================================================
// cron_clustering.php
// Script CLI PHP untuk menjalankan clustering otomatis via cronjob
// =====================================================

require_once __DIR__ . '/../config/config.php';

// Path folder dataset clustering
$uploadDir = __DIR__ . '/../dashboard/assets/file_clustering/';

if (!is_dir($uploadDir)) {
    echo "Folder dataset tidak ditemukan: $uploadDir\n";
    exit(1);
}

// Ambil file CSV terbaru berdasarkan waktu modifikasi
$files = glob($uploadDir . '*.csv');
if (empty($files)) {
    echo "Tidak ada file CSV untuk clustering.\n";
    exit(0); // Normal exit, hanya tidak ada file baru
}

$latestFile = '';
$latestTime = 0;
foreach ($files as $file) {
    $modTime = filemtime($file);
    if ($modTime > $latestTime) {
        $latestTime = $modTime;
        $latestFile = $file;
    }
}

if (!$latestFile || !is_readable($latestFile)) {
    echo "File terbaru tidak dapat dibaca: $latestFile\n";
    exit(1);
}

// Sesuaikan path PHP CLI di server hosting Anda
$phpPath = 'php'; // Asumsi PHP sudah di PATH, bisa juga path absolut: /usr/bin/php

// Path script kmeans.php
$scriptPath = realpath(__DIR__ . '/../ml/kmeans.php');
if (!$scriptPath) {
    echo "Script kmeans.php tidak ditemukan.\n";
    exit(1);
}

// Siapkan command untuk eksekusi clustering
$command = escapeshellcmd("$phpPath $scriptPath $latestFile") . ' 2>&1';

// Jalankan clustering
exec($command, $output, $status);

echo implode("\n", $output) . "\n";

if ($status !== 0) {
    echo "Error: proses clustering gagal dengan status: $status\n";
    exit(1);
}

$jsonData = implode("\n", $output);
$result = json_decode($jsonData, true);

if (!$result || !isset($result['rows'])) {
    echo "Error: output clustering tidak valid atau JSON gagal didecode.\n";
    exit(1);
}

// TODO: Tambahkan kode simpan hasil ke database di sini
// Contoh:
// simpan_hasil_clustering($result['rows']);

echo "Clustering selesai untuk file: " . basename($latestFile) . "\n";

exit(0);

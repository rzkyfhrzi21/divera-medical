<?php
// =====================================================
// function_clustering.php (FINAL VERSION - PART 1)
// Controller proses clustering SIP Kafe
// 
// Fitur:
// - Upload & validasi CSV dataset (EXTRA STRICT)
// - Implementasi Weighted Sum Model (WSM)
// - K-Means Clustering (PHP Native - NO EXEC)
// - Auto-save hasil ke database
// - Log hasil clustering ke file txt
// - COCOK untuk shared hosting
// 
// Author: Delin Palentin
// Updated: 2026
// =====================================================

session_start();
require_once '../config/config.php';

// =====================================================
// HELPER FUNCTION: REDIRECT DENGAN SESSION
// =====================================================
function redirect_with_message($page, $status, $action, $ket, $msg = '')
{
    $_SESSION['flash_status'] = $status;
    $_SESSION['flash_action'] = $action;
    $_SESSION['flash_ket'] = $ket;
    if (!empty($msg)) {
        $_SESSION['flash_msg'] = $msg;
    }
    header("Location: ../dashboard/admin?page=$page");
    exit;
}

// =====================================================
// TAHAP 0: VALIDASI REQUEST & BOBOT WSM
// =====================================================

// Cek apakah form di-submit
if (!isset($_POST['btn_mulai_clustering'])) {
    redirect_with_message('Mulai Clustering', 'error', 'clustering', 'invalid_request');
}

// =====================================================
// VALIDASI & AMBIL BOBOT WSM DARI FORM
// =====================================================

$bobot_rasa      = isset($_POST['bobot_rasa']) ? (float)$_POST['bobot_rasa'] : 0;
$bobot_pelayanan = isset($_POST['bobot_pelayanan']) ? (float)$_POST['bobot_pelayanan'] : 0;
$bobot_fasilitas = isset($_POST['bobot_fasilitas']) ? (float)$_POST['bobot_fasilitas'] : 0;
$bobot_suasana   = isset($_POST['bobot_suasana']) ? (float)$_POST['bobot_suasana'] : 0;
$bobot_harga     = isset($_POST['bobot_harga']) ? (float)$_POST['bobot_harga'] : 0;
$bobot_rating    = isset($_POST['bobot_rating']) ? (float)$_POST['bobot_rating'] : 0;

// Hitung total bobot (harus = 100%)
$total_bobot = $bobot_rasa + $bobot_pelayanan + $bobot_fasilitas +
    $bobot_suasana + $bobot_harga + $bobot_rating;

// Validasi total bobot
if ($total_bobot != 100) {
    redirect_with_message('Mulai Clustering', 'error', 'clustering', 'invalid_weight_total', 'Total bobot harus 100%');
}

// Konversi bobot ke desimal (0-1) untuk perhitungan WSM
// Rumus WSM: Vi = Σ(Wj × Xij)
$w_rasa      = $bobot_rasa / 100;
$w_pelayanan = $bobot_pelayanan / 100;
$w_fasilitas = $bobot_fasilitas / 100;
$w_suasana   = $bobot_suasana / 100;
$w_harga     = $bobot_harga / 100;
$w_rating    = $bobot_rating / 100;

// =====================================================
// VALIDASI FILE UPLOAD
// =====================================================

if (!isset($_FILES['dataset_csv']) || $_FILES['dataset_csv']['error'] !== 0) {
    redirect_with_message('Mulai Clustering', 'warning', 'clustering', 'file_missing');
}

// =====================================================
// TAHAP 1: SETUP & SIMPAN FILE DATASET
// =====================================================

$uploadDir = '../dashboard/assets/file_clustering/';

// Buat folder jika belum ada
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$originalName = basename($_FILES['dataset_csv']['name']);
$targetPath   = $uploadDir . $originalName;

// Upload file ke server
if (!move_uploaded_file($_FILES['dataset_csv']['tmp_name'], $targetPath)) {
    redirect_with_message('Mulai Clustering', 'error', 'clustering', 'upload_failed');
}

// =====================================================
// TAHAP 2: BUKA FILE CSV
// =====================================================

$handle = fopen($targetPath, 'r');
if (!$handle) {
    redirect_with_message('Mulai Clustering', 'error', 'clustering', 'file_unreadable');
}

// =====================================================
// TAHAP 3: VALIDASI HEADER CSV (WAJIB SESUAI TEMPLATE)
// =====================================================

// Header yang diharapkan (sesuai template sistem)
$expectedHeader = [
    'Nama Kafe',
    'Skor_Rasa',
    'Skor_Pelayanan',
    'Skor_Fasilitas',
    'Skor_Suasana',
    'Skor_Harga',
    'Skor_Rating'
];

// Ambil baris pertama untuk deteksi delimiter
$firstLine = fgets($handle);

// =====================================================
// AUTO DETECT DELIMITER ( ; , TAB )
// =====================================================

$delimiters = [
    ';'  => substr_count($firstLine, ';'),
    ','  => substr_count($firstLine, ','),
    "\t" => substr_count($firstLine, "\t")
];

// Ambil delimiter dengan jumlah terbanyak
$max_count = max($delimiters);

// VALIDASI: Pastikan ada delimiter yang terdeteksi
if ($max_count === 0) {
    fclose($handle);
    redirect_with_message('Mulai Clustering', 'error', 'clustering', 'invalid_header', 'File CSV tidak valid. Delimiter tidak ditemukan.');
}

$delimiter = array_search($max_count, $delimiters);

// Fallback ke semicolon jika tidak terdeteksi
if (!$delimiter) {
    $delimiter = ';';
}

// Reset pointer file ke awal
rewind($handle);

// VALIDASI: Pastikan delimiter yang terdeteksi menghasilkan 7 kolom
$test_row = fgetcsv($handle, 1000, $delimiter);
if (count($test_row) !== 7) {
    fclose($handle);
    redirect_with_message('Mulai Clustering', 'error', 'clustering', 'invalid_header', 'Jumlah kolom tidak sesuai. Harus 7 kolom.');
}

// Reset lagi ke awal
rewind($handle);

// Ambil header CSV sesuai delimiter
$csvHeader = fgetcsv($handle, 1000, $delimiter);

// VALIDASI TAMBAHAN: Cek apakah delimiter sudah benar
// dengan melihat apakah header pertama tidak mengandung delimiter lain
$header_first = trim(preg_replace('/^\xEF\xBB\xBF/', '', $csvHeader[0]));

if (strpos($header_first, ';') !== false && $delimiter !== ';') {
    fclose($handle);
    redirect_with_message('Mulai Clustering', 'error', 'clustering', 'invalid_header', 'Delimiter CSV tidak sesuai. Gunakan semicolon (;)');
}

if (strpos($header_first, ',') !== false && $delimiter !== ',') {
    fclose($handle);
    redirect_with_message('Mulai Clustering', 'error', 'clustering', 'invalid_header', 'Delimiter CSV tidak sesuai. Gunakan comma (,)');
}

// Normalisasi header (trim + lowercase + hapus BOM)
$csvHeaderNormalized = array_map(
    fn($h) => strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', $h))),
    $csvHeader
);

$expectedHeaderNormalized = array_map(
    fn($h) => strtolower(trim($h)),
    $expectedHeader
);

// Validasi jumlah kolom
if (count($csvHeaderNormalized) !== count($expectedHeaderNormalized)) {
    fclose($handle);
    redirect_with_message('Mulai Clustering', 'warning', 'clustering', 'invalid_column_count');
}

// Validasi isi & urutan header
if ($csvHeaderNormalized !== $expectedHeaderNormalized) {
    fclose($handle);
    redirect_with_message('Mulai Clustering', 'warning', 'clustering', 'invalid_header');
}

// =====================================================
// TAHAP 3.1: RENAME FILE DATASET (SETELAH HEADER VALID)
// =====================================================

// Tutup file sementara
fclose($handle);

// Buat nama file resmi sesuai format sistem
// Format: YYYY-MM-DD_HH-MM-SS_Clustering_SIP_Kafe_Balam.csv
$finalFileName = date('Y-m-d_H-i-s') . '_Clustering_SIP_Kafe_Balam.csv';
$finalPath     = $uploadDir . $finalFileName;

// Rename file
rename($targetPath, $finalPath);

// Update path file untuk proses selanjutnya
$targetPath = $finalPath;

// Buka ulang file CSV yang sudah di-rename
$handle = fopen($targetPath, 'r');

// Lewati header (karena sudah divalidasi)
fgetcsv($handle, 1000, $delimiter);

// =====================================================
// TAHAP 3.2: RESET DATA HASIL (TANPA HAPUS RIWAYAT)
// - kafe        : TIDAK DIHAPUS (master data)
// - clustering  : TIDAK DIHAPUS (riwayat proses)
// - hasil_*     : DIHAPUS SEMUA (data lama)
// =====================================================

// Nonaktifkan foreign key check sementara
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS=0");

// Hapus semua data hasil clustering & kuisioner lama
mysqli_query($koneksi, "TRUNCATE TABLE hasil_clustering");
mysqli_query($koneksi, "TRUNCATE TABLE hasil_kuisioner");

// Aktifkan kembali foreign key check
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS=1");

// =====================================================
// TAHAP 4: IMPORT DATA CSV KE DATABASE + HITUNG WSM
// =====================================================

$jumlahData = 0;
$data_kafe_for_clustering = []; // Untuk proses K-Means
$skipped_rows = 0; // Counter data yang di-skip

// Loop setiap baris data CSV
while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {

    // VALIDASI 1: Skip baris kosong
    if (empty($row[0])) {
        $skipped_rows++;
        continue;
    }

    // VALIDASI 2: Pastikan ada minimal 7 kolom
    if (count($row) < 7) {
        $skipped_rows++;
        continue; // Skip baris yang tidak lengkap
    }

    // Ambil data dari CSV
    $nama_kafe_raw = trim($row[0]);

    // VALIDASI 3: Pastikan nama kafe tidak mengandung delimiter
    // Jika ada delimiter di nama, berarti parsing salah
    if (
        strpos($nama_kafe_raw, ';') !== false ||
        strpos($nama_kafe_raw, ',') !== false
    ) {
        $skipped_rows++;
        error_log("Data corrupt detected (delimiter in name): " . $nama_kafe_raw);
        continue; // Skip data yang corrupt
    }

    // VALIDASI 4: Pastikan nama kafe tidak mengandung pattern angka di akhir
    // (ciri khas data tercampur seperti "20 Kopi Purnawirawan;5;5;5")
    if (preg_match('/[;\,]\d+/', $nama_kafe_raw)) {
        $skipped_rows++;
        error_log("Data corrupt detected (pattern): " . $nama_kafe_raw);
        continue; // Skip data corrupt
    }

    $nama_kafe = $nama_kafe_raw;

    // Ambil data numerik dengan validasi ketat
    $rasa      = isset($row[1]) ? trim($row[1]) : '';
    $pelayanan = isset($row[2]) ? trim($row[2]) : '';
    $fasilitas = isset($row[3]) ? trim($row[3]) : '';
    $suasana   = isset($row[4]) ? trim($row[4]) : '';
    $harga     = isset($row[5]) ? trim($row[5]) : '';
    $rating    = isset($row[6]) ? trim($row[6]) : '';

    // VALIDASI 5: Pastikan semua nilai numerik valid (bukan string)
    if (
        !is_numeric($rasa) || !is_numeric($pelayanan) ||
        !is_numeric($fasilitas) || !is_numeric($suasana) ||
        !is_numeric($harga) || !is_numeric($rating)
    ) {
        $skipped_rows++;
        error_log("Data corrupt detected (non-numeric): " . $nama_kafe);
        continue; // Skip data yang bukan angka
    }

    // Konversi ke float
    $rasa      = (float)$rasa;
    $pelayanan = (float)$pelayanan;
    $fasilitas = (float)$fasilitas;
    $suasana   = (float)$suasana;
    $harga     = (float)$harga;
    $rating    = (float)$rating;

    // VALIDASI 6: Skip jika ada nilai 0 atau negatif
    if (
        $rasa <= 0 || $pelayanan <= 0 || $fasilitas <= 0 ||
        $suasana <= 0 || $harga <= 0 || $rating <= 0
    ) {
        $skipped_rows++;
        continue;
    }

    // VALIDASI 7: Skip jika nilai di luar range (1-5)
    if (
        $rasa > 5 || $pelayanan > 5 || $fasilitas > 5 ||
        $suasana > 5 || $harga > 5 || $rating > 5
    ) {
        $skipped_rows++;
        continue;
    }

    // ===============================
    // HITUNG NILAI WSM (WEIGHTED SUM MODEL)
    // ===============================
    // Rumus: Vi = Σ(Wj × Xij)
    // Vi = nilai akhir alternatif ke-i
    // Wj = bobot kriteria ke-j
    // Xij = nilai alternatif ke-i pada kriteria ke-j
    // ===============================

    $nilai_wsm = ($w_rasa * $rasa) +
        ($w_pelayanan * $pelayanan) +
        ($w_fasilitas * $fasilitas) +
        ($w_suasana * $suasana) +
        ($w_harga * $harga) +
        ($w_rating * $rating);

    // Bulatkan 2 desimal
    $nilai_wsm_normalized = round($nilai_wsm, 2);

    // ===============================
    // CEK APAKAH KAFE SUDAH ADA DI DATABASE
    // ===============================

    $nama_kafe_db = mysqli_real_escape_string($koneksi, $nama_kafe);

    $cek = mysqli_query(
        $koneksi,
        "SELECT id_kafe FROM kafe 
         WHERE LOWER(nama_kafe) = LOWER('$nama_kafe_db')"
    );

    if (mysqli_num_rows($cek) > 0) {
        // Kafe sudah ada, ambil ID-nya
        $data    = mysqli_fetch_assoc($cek);
        $id_kafe = $data['id_kafe'];
    } else {
        // Kafe belum ada, insert baru dengan data default
        mysqli_query(
            $koneksi,
            "INSERT INTO kafe 
            (nama_kafe, alamat, harga_terendah, harga_tertinggi, foto_kafe)
            VALUES
            ('" . mysqli_real_escape_string($koneksi, ucwords(strtolower($nama_kafe))) . "',
             'Belum diisi', 1, 1, 'default.jpg')"
        );
        $id_kafe = mysqli_insert_id($koneksi);
    }

    // ===============================
    // INSERT HASIL KUISIONER + NILAI WSM
    // ===============================

    mysqli_query(
        $koneksi,
        "INSERT INTO hasil_kuisioner
        (id_kafe, rasa_kopi, pelayanan, fasilitas, suasana, harga, rating, nilai_wsm)
        VALUES
        ($id_kafe, $rasa, $pelayanan, $fasilitas, $suasana, $harga, $rating, $nilai_wsm_normalized)"
    );

    // ===============================
    // SIMPAN DATA UNTUK K-MEANS CLUSTERING
    // ===============================

    $nama_normalized = ucwords(strtolower($nama_kafe));

    if (!isset($data_kafe_for_clustering[$nama_normalized])) {
        $data_kafe_for_clustering[$nama_normalized] = [
            'id_kafe' => $id_kafe,
            'rasa' => [],
            'pelayanan' => [],
            'fasilitas' => [],
            'suasana' => [],
            'harga' => []
        ];
    }

    $data_kafe_for_clustering[$nama_normalized]['rasa'][] = $rasa;
    $data_kafe_for_clustering[$nama_normalized]['pelayanan'][] = $pelayanan;
    $data_kafe_for_clustering[$nama_normalized]['fasilitas'][] = $fasilitas;
    $data_kafe_for_clustering[$nama_normalized]['suasana'][] = $suasana;
    $data_kafe_for_clustering[$nama_normalized]['harga'][] = $harga;

    $jumlahData++;
}

// Tutup file CSV
fclose($handle);

// =====================================================
// TAHAP 5: INSERT RECORD CLUSTERING KE DATABASE
// =====================================================

$namaFileClustering = $finalFileName;

$ins = mysqli_query(
    $koneksi,
    "INSERT INTO clustering
    (nama_file, jumlah_cluster, jumlah_data, waktu_clustering)
    VALUES
    ('" . mysqli_real_escape_string($koneksi, $namaFileClustering) . "',
     3,
     $jumlahData,
     NOW())"
);

if (!$ins) {
    redirect_with_message('Mulai Clustering', 'error', 'clustering', 'insert_clustering_failed');
}

// Ambil ID clustering yang baru saja dibuat
$idClusterBaru = mysqli_insert_id($koneksi);

// =====================================================
// END OF PART 1
// =====================================================

// =====================================================
// PART 2: TAHAP 6 - 10 (Lanjutan dari PART 1)
// Gabungkan dengan PART 1 untuk file lengkap
// =====================================================

// =====================================================
// TAHAP 6: JALANKAN K-MEANS CLUSTERING (PHP NATIVE)
// =====================================================

// Agregasi data per kafe (rata-rata jika ada duplikat)
$df_group = [];
foreach ($data_kafe_for_clustering as $nama => $values) {
    $df_group[] = [
        'nama_kafe' => $nama,
        'id_kafe' => $values['id_kafe'],
        'rasa' => array_sum($values['rasa']) / count($values['rasa']),
        'pelayanan' => array_sum($values['pelayanan']) / count($values['pelayanan']),
        'fasilitas' => array_sum($values['fasilitas']) / count($values['fasilitas']),
        'suasana' => array_sum($values['suasana']) / count($values['suasana']),
        'harga' => array_sum($values['harga']) / count($values['harga'])
    ];
}

// Validasi jumlah data
$k = 3; // Jumlah cluster
if (count($df_group) < $k) {
    redirect_with_message('Mulai Clustering', 'error', 'clustering', 'kmeans_error', 'Jumlah kafe kurang dari jumlah cluster (minimum 3 kafe)');
}

// ===============================
// NORMALISASI MIN-MAX
// ===============================
// Rumus: X_norm = (X - X_min) / (X_max - X_min)
// ===============================

$fitur = ['rasa', 'pelayanan', 'fasilitas', 'suasana', 'harga'];
$min_max = [];

// Cari min & max per fitur
foreach ($fitur as $f) {
    $values = array_column($df_group, $f);
    $min_max[$f] = [
        'min' => min($values),
        'max' => max($values)
    ];
}

// Normalisasi data
$X = [];
foreach ($df_group as $i => $row) {
    $X[$i] = [];
    foreach ($fitur as $f) {
        $min = $min_max[$f]['min'];
        $max = $min_max[$f]['max'];

        // Min-Max Scaling
        if ($max - $min == 0) {
            $X[$i][] = 0; // Semua nilai sama
        } else {
            $X[$i][] = ($row[$f] - $min) / ($max - $min);
        }
    }
}

// ===============================
// K-MEANS++ INITIALIZATION
// ===============================
// Pilih centroid awal dengan algoritma K-Means++
// ===============================

$centroids = [];

// Pilih centroid pertama secara random
$centroids[0] = $X[array_rand($X)];

// Pilih centroid berikutnya
for ($c = 1; $c < $k; $c++) {
    $distances = [];

    // Hitung jarak setiap titik ke centroid terdekat
    foreach ($X as $i => $point) {
        $min_dist = PHP_FLOAT_MAX;

        foreach ($centroids as $centroid) {
            $dist = 0;
            for ($j = 0; $j < count($point); $j++) {
                $dist += pow($point[$j] - $centroid[$j], 2);
            }
            $dist = sqrt($dist);

            if ($dist < $min_dist) {
                $min_dist = $dist;
            }
        }

        $distances[$i] = $min_dist;
    }

    // Pilih titik dengan jarak terjauh sebagai centroid baru
    $max_index = array_search(max($distances), $distances);
    $centroids[$c] = $X[$max_index];
}

// ===============================
// K-MEANS ITERASI
// ===============================

$labels = [];
$max_iter = 100;

for ($iter = 0; $iter < $max_iter; $iter++) {

    // Assign data ke cluster terdekat
    $new_labels = [];
    foreach ($X as $i => $point) {
        $min_dist = PHP_FLOAT_MAX;
        $cluster = 0;

        foreach ($centroids as $c => $centroid) {
            $dist = 0;
            for ($j = 0; $j < count($point); $j++) {
                $dist += pow($point[$j] - $centroid[$j], 2);
            }
            $dist = sqrt($dist);

            if ($dist < $min_dist) {
                $min_dist = $dist;
                $cluster = $c;
            }
        }

        $new_labels[$i] = $cluster;
    }

    // Cek konvergensi (jika label tidak berubah, stop)
    if ($labels === $new_labels) {
        break;
    }

    $labels = $new_labels;

    // Update centroid (rata-rata semua data di cluster)
    $new_centroids = [];
    for ($c = 0; $c < $k; $c++) {
        $cluster_points = [];

        foreach ($labels as $i => $label) {
            if ($label === $c) {
                $cluster_points[] = $X[$i];
            }
        }

        if (empty($cluster_points)) {
            // Cluster kosong, centroid tidak berubah
            $new_centroids[$c] = $centroids[$c];
        } else {
            // Hitung rata-rata per dimensi
            $new_centroids[$c] = [];
            for ($j = 0; $j < count($cluster_points[0]); $j++) {
                $sum = 0;
                foreach ($cluster_points as $point) {
                    $sum += $point[$j];
                }
                $new_centroids[$c][] = $sum / count($cluster_points);
            }
        }
    }

    $centroids = $new_centroids;
}

// ===============================
// HITUNG JARAK KE CENTROID
// ===============================

$jarak = [];
foreach ($X as $i => $point) {
    $centroid = $centroids[$labels[$i]];
    $dist = 0;
    for ($j = 0; $j < count($point); $j++) {
        $dist += pow($point[$j] - $centroid[$j], 2);
    }
    $jarak[$i] = sqrt($dist);
}

// ===============================
// RANKING PER CLUSTER
// ===============================
// Ranking berdasarkan jarak (terdekat = rank 1)
// ===============================

$cluster_data = [];
foreach ($labels as $i => $cluster) {
    $cluster_data[$cluster][] = [
        'index' => $i,
        'jarak' => $jarak[$i]
    ];
}

$peringkat = [];
foreach ($cluster_data as $cluster => $items) {
    // Sort by jarak (ascending = terdekat dulu)
    usort($items, function ($a, $b) {
        return $a['jarak'] <=> $b['jarak'];
    });

    // Assign ranking
    $rank = 1;
    foreach ($items as $item) {
        $peringkat[$item['index']] = $rank++;
    }
}

// =====================================================
// TAHAP 7: SIMPAN HASIL CLUSTERING KE DATABASE
// =====================================================

foreach ($df_group as $i => $row) {
    $id_kafe = $row['id_kafe'];

    // Hitung rating akhir (rata-rata rating dari kuisioner)
    $q_rating = mysqli_query(
        $koneksi,
        "SELECT ROUND(AVG(rating), 2) as avg_rating
         FROM hasil_kuisioner
         WHERE id_kafe = $id_kafe"
    );

    $rating_data = mysqli_fetch_assoc($q_rating);
    $rating_akhir = $rating_data['avg_rating'] ?? 0;

    // Insert hasil clustering
    mysqli_query(
        $koneksi,
        "INSERT INTO hasil_clustering
        (id_cluster, id_kafe, cluster, jarak_centroid, peringkat_cluster, rating_akhir)
        VALUES
        (
            $idClusterBaru,
            $id_kafe,
            " . ($labels[$i] + 1) . ",
            " . round($jarak[$i], 6) . ",
            {$peringkat[$i]},
            $rating_akhir
        )"
    );
}

// =====================================================
// TAHAP 8: LOG HASIL CLUSTERING KE FILE TXT
// =====================================================

$log_file_path = $uploadDir . 'log_clustering.txt';
$log_content = "";

$log_content .= "=====================================================\n";
$log_content .= "LOG CLUSTERING - SIP KAFE BALAM\n";
$log_content .= "=====================================================\n";
$log_content .= "Tanggal: " . date('Y-m-d H:i:s') . "\n";
$log_content .= "File: $namaFileClustering\n";
$log_content .= "-----------------------------------------------------\n\n";

$log_content .= "PARAMETER WSM:\n";
$log_content .= "- Bobot Rasa Kopi: $bobot_rasa%\n";
$log_content .= "- Bobot Pelayanan: $bobot_pelayanan%\n";
$log_content .= "- Bobot Fasilitas: $bobot_fasilitas%\n";
$log_content .= "- Bobot Suasana: $bobot_suasana%\n";
$log_content .= "- Bobot Harga: $bobot_harga%\n";
$log_content .= "- Bobot Rating: $bobot_rating%\n";
$log_content .= "Total Bobot: 100%\n\n";

$log_content .= "STATISTIK DATA:\n";
$log_content .= "- Total Baris Diproses: " . ($jumlahData + $skipped_rows) . "\n";
$log_content .= "- Data Valid: $jumlahData kafe\n";
$log_content .= "- Data Di-skip (corrupt/invalid): $skipped_rows\n";
$log_content .= "- Jumlah Cluster: $k\n";
$log_content .= "- Iterasi K-Means: " . ($iter + 1) . "\n\n";

$log_content .= "HASIL CLUSTERING:\n";
$log_content .= "-----------------------------------------------------\n";

// Kelompokkan per cluster
for ($c = 1; $c <= $k; $c++) {
    $log_content .= "\nCLUSTER $c:\n";
    $count = 0;
    foreach ($df_group as $i => $row) {
        if (($labels[$i] + 1) == $c) {
            $count++;
            $log_content .= sprintf(
                "  %2d. %-40s | Jarak: %.6f | Rank: %d\n",
                $count,
                $row['nama_kafe'],
                $jarak[$i],
                $peringkat[$i]
            );
        }
    }
    $log_content .= "  Total: $count kafe\n";
}

$log_content .= "\n=====================================================\n";
$log_content .= "END OF LOG\n";
$log_content .= "=====================================================\n\n";

// Tulis ke file (append mode)
file_put_contents($log_file_path, $log_content, FILE_APPEND);

// =====================================================
// TAHAP 9: LOG AKTIVITAS DATABASE (OPSIONAL)
// =====================================================

// Simpan log proses clustering untuk audit trail
$log_message = "Clustering berhasil | File: $namaFileClustering | Jumlah Data: $jumlahData | K=3 | Bobot: R=$bobot_rasa%, P=$bobot_pelayanan%, F=$bobot_fasilitas%, S=$bobot_suasana%, H=$bobot_harga%, RT=$bobot_rating%";

if (isset($_SESSION['sesi_id'])) {
    mysqli_query(
        $koneksi,
        "INSERT INTO log_aktivitas (id_admin, aktivitas, waktu)
         VALUES (
             {$_SESSION['sesi_id']},
             '" . mysqli_real_escape_string($koneksi, $log_message) . "',
             NOW()
         )"
    );
}

// =====================================================
// TAHAP 10: SELESAI - REDIRECT KE HALAMAN HASIL
// =====================================================

$_SESSION['flash_total'] = $jumlahData;
redirect_with_message('Hasil Clustering', 'success', 'clustering', 'process_completed');

// =====================================================
// END OF FILE
// =====================================================
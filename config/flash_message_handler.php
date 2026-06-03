<?php
// =====================================================
// flash_message_handler.php
// Helper untuk menampilkan pesan dari SESSION
// Lokasi: /config/flash_message_handler.php
// Menggunakan SweetAlert2
// =====================================================

/**
 * Fungsi untuk menampilkan flash message dari session
 * Digunakan untuk menampilkan pesan error/success setelah redirect
 */
function show_flash_message()
{
    if (!isset($_SESSION)) {
        session_start();
    }

    // Cek apakah ada flash message
    if (isset($_SESSION['flash_status'])) {
        $status = $_SESSION['flash_status'];
        $action = $_SESSION['flash_action'] ?? '';
        $ket = $_SESSION['flash_ket'] ?? '';
        $msg = $_SESSION['flash_msg'] ?? '';
        $total = $_SESSION['flash_total'] ?? 0;

        // Tentukan icon SweetAlert
        $icon = 'info';
        $title = 'Informasi';

        switch ($status) {
            case 'success':
                $icon = 'success';
                $title = 'Berhasil!';
                break;
            case 'error':
                $icon = 'error';
                $title = 'Gagal!';
                break;
            case 'warning':
                $icon = 'warning';
                $title = 'Peringatan!';
                break;
        }

        // Tentukan pesan berdasarkan ket
        $message = '';
        switch ($ket) {
            case 'invalid_request':
                $message = 'Request tidak valid. Silakan coba lagi.';
                break;
            case 'invalid_weight_total':
                $message = 'Total bobot harus 100%. ' . $msg;
                break;
            case 'file_missing':
                $message = 'File CSV tidak ditemukan atau gagal diupload.';
                break;
            case 'upload_failed':
                $message = 'Gagal mengupload file. Pastikan format file benar.';
                break;
            case 'file_unreadable':
                $message = 'File tidak dapat dibaca. Pastikan file tidak corrupt.';
                break;
            case 'invalid_column_count':
                $message = 'Jumlah kolom CSV tidak sesuai. Harus ada 7 kolom: Nama Kafe, Skor_Rasa, Skor_Pelayanan, Skor_Fasilitas, Skor_Suasana, Skor_Harga, Skor_Rating';
                break;
            case 'invalid_header':
                $message = 'Header CSV tidak sesuai. Gunakan format: Nama Kafe;Skor_Rasa;Skor_Pelayanan;Skor_Fasilitas;Skor_Suasana;Skor_Harga;Skor_Rating';
                break;
            case 'insert_clustering_failed':
                $message = 'Gagal menyimpan record clustering ke database.';
                break;
            case 'kmeans_error':
                $message = 'Proses K-Means gagal. Detail: ' . $msg;
                break;
            case 'invalid_kmeans_output':
                $message = 'Output K-Means tidak valid. ' . $msg;
                break;
            case 'process_completed':
                $message = "Proses clustering berhasil! Total data yang diproses: $total kafe.";
                break;
            default:
                $message = $msg ? $msg : 'Terjadi kesalahan yang tidak diketahui.';
        }

        // Escape message untuk JavaScript
        $message_escaped = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        $title_escaped = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        // Tampilkan SweetAlert
        echo <<<HTML
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '{$icon}',
                title: '{$title_escaped}',
                html: '{$message_escaped}',
                confirmButtonColor: '#435ebe',
                confirmButtonText: 'OK',
                timer: 3000,
            });
        });
        </script>
HTML;

        // Hapus flash message dari session
        unset($_SESSION['flash_status']);
        unset($_SESSION['flash_action']);
        unset($_SESSION['flash_ket']);
        unset($_SESSION['flash_msg']);
        unset($_SESSION['flash_total']);
    }
}

// Auto-execute jika dipanggil langsung
if (basename($_SERVER['PHP_SELF']) === 'flash_message_handler.php') {
    show_flash_message();
}

    <?php
    include "../config/config.php";

    if (isset($_POST['btn_mulai_wsm'])) {

        // Cek apakah file ada
        if (!isset($_FILES['wsm_csv']) || $_FILES['wsm_csv']['error'] != 0) {
            header("Location: ../dashboard/admin?page=Mulai Clustering&status=error&action=uploadcsvwsm&ket=file_not_found");
            exit;
        }

        $nama_file = $_FILES['wsm_csv']['name'];
        $tmp_file  = $_FILES['wsm_csv']['tmp_name'];

        // Buka file CSV
        if (($handle = fopen($tmp_file, "r")) !== FALSE) {

            $row = 0;

            $total_rasa = 0;
            $total_pelayanan = 0;
            $total_fasilitas = 0;
            $total_suasana = 0;
            $total_harga = 0;
            $total_rating = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                if ($row == 0) {
                    $row++;
                    continue;
                }

                // Pastikan kolom cukup
                if (count($data) < 10) {
                    continue; // skip baris yang tidak valid
                }

                $total_rasa      += floatval($data[4]);
                $total_pelayanan += floatval($data[5]);
                $total_fasilitas += floatval($data[6]);
                $total_suasana   += floatval($data[7]);
                $total_harga     += floatval($data[8]);
                $total_rating    += floatval($data[9]);

                $row++;
            }

            fclose($handle);

            $jumlah_responden = $row - 1;

            if ($jumlah_responden <= 0) {
                header("Location: ../dashboard/admin?page=Mulai Clustering&status=error&action=upload&ket=data_kosong");
                exit;
            }

            // =========================
            // HITUNG RATA-RATA
            // =========================
            $rasa      = $total_rasa / $jumlah_responden;
            $pelayanan = $total_pelayanan / $jumlah_responden;
            $fasilitas = $total_fasilitas / $jumlah_responden;
            $suasana   = $total_suasana / $jumlah_responden;
            $harga     = $total_harga / $jumlah_responden;
            $rating    = $total_rating / $jumlah_responden;

            // =========================
            // HITUNG TOTAL MEAN
            // =========================
            $total_mean = $rasa + $pelayanan + $fasilitas + $suasana + $harga + $rating;

            // =========================
            // HITUNG BOBOT (NORMALISASI)
            // =========================
            $bobot_rasa      = $rasa / $total_mean;
            $bobot_pelayanan = $pelayanan / $total_mean;
            $bobot_fasilitas = $fasilitas / $total_mean;
            $bobot_suasana   = $suasana / $total_mean;
            $bobot_harga     = $harga / $total_mean;
            $bobot_rating    = $rating / $total_mean;

            // Format sesuai DECIMAL(6,4)
            $bobot_rasa      = number_format($bobot_rasa, 4, '.', '');
            $bobot_pelayanan = number_format($bobot_pelayanan, 4, '.', '');
            $bobot_fasilitas = number_format($bobot_fasilitas, 4, '.', '');
            $bobot_suasana   = number_format($bobot_suasana, 4, '.', '');
            $bobot_harga     = number_format($bobot_harga, 4, '.', '');
            $bobot_rating    = number_format($bobot_rating, 4, '.', '');

            // =========================
            // INSERT KE DATABASE
            // =========================
            $query = mysqli_query($koneksi, "INSERT INTO hasil_wsm (
                nama_file,
                rasa_kopi,
                pelayanan,
                fasilitas,
                suasana,
                harga,
                rating,
                bobot_rasa,
                bobot_pelayanan,
                bobot_fasilitas,
                bobot_suasana,
                bobot_harga,
                bobot_rating
            ) VALUES (
                '$nama_file',
                '$rasa',
                '$pelayanan',
                '$fasilitas',
                '$suasana',
                '$harga',
                '$rating',
                '$bobot_rasa',
                '$bobot_pelayanan',
                '$bobot_fasilitas',
                '$bobot_suasana',
                '$bobot_harga',
                '$bobot_rating'
            )");

            if ($query) {
                header("Location: ../dashboard/admin?page=Mulai Clustering&status=success&action=hitung&ket=berhasil");
            } else {
                header("Location: ../dashboard/admin?page=Mulai Clustering&status=error&action=hitung&ket=query_failed");
            }
        } else {
            header("Location: ../dashboard/admin?page=Mulai Clustering&status=error&action=upload&ket=file_error");
        }
    }

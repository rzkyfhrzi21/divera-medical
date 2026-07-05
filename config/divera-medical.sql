-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2026 at 08:40 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `divera-medical`
--

-- --------------------------------------------------------

--
-- Table structure for table `dokter`
--

CREATE TABLE `dokter` (
  `id` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `spesialisasi` varchar(100) NOT NULL,
  `nomor_izin_praktik` varchar(50) DEFAULT NULL,
  `biografi` text DEFAULT NULL,
  `tahun_pengalaman` int(11) DEFAULT 0,
  `biaya` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dokter`
--

INSERT INTO `dokter` (`id`, `id_pengguna`, `spesialisasi`, `nomor_izin_praktik`, `biografi`, `tahun_pengalaman`, `biaya`) VALUES
(1, 2, '', NULL, NULL, 0, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `janji_temu`
--

CREATE TABLE `janji_temu` (
  `id` int(11) NOT NULL,
  `id_dokter` int(11) NOT NULL,
  `id_pasien` int(11) NOT NULL,
  `tanggal_janji` datetime NOT NULL,
  `status` enum('menunggu','dikonfirmasi','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu',
  `gejala` text DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `janji_temu`
--

INSERT INTO `janji_temu` (`id`, `id_dokter`, `id_pasien`, `tanggal_janji`, `status`, `gejala`, `catatan`, `dibuat_pada`) VALUES
(2, 1, 2, '2026-06-11 12:45:00', 'menunggu', '11111111111111111111', NULL, '2026-06-11 05:46:06');

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `id` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `kuantitas` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keranjang`
--

INSERT INTO `keranjang` (`id`, `id_pengguna`, `id_produk`, `kuantitas`, `created_at`) VALUES
(4, 4, 2, 3, '2026-06-11 05:46:43'),
(5, 4, 4, 1, '2026-06-11 05:47:43'),
(6, 4, 1, 1, '2026-06-11 05:48:06');

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `id` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('laki-laki','perempuan','lainnya') DEFAULT NULL,
  `golongan_darah` varchar(5) DEFAULT NULL,
  `berat_badan` decimal(5,2) DEFAULT NULL,
  `tinggi_badan` decimal(5,2) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `bpjs` enum('ya','tidak') DEFAULT 'tidak'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pasien`
--

INSERT INTO `pasien` (`id`, `id_pengguna`, `tanggal_lahir`, `jenis_kelamin`, `golongan_darah`, `berat_badan`, `tinggi_badan`, `alamat`, `bpjs`) VALUES
(2, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'tidak');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','dokter','pasien') NOT NULL DEFAULT 'pasien',
  `telpon` varchar(20) DEFAULT NULL,
  `tgl_daftar` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto_profil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id`, `nama`, `email`, `password`, `role`, `telpon`, `tgl_daftar`, `foto_profil`) VALUES
(2, 'Tata Difa', 'tata@gmail.com', '$2y$10$VcREM9D9y/oldpKECMVy2eVIXCn4gUPhzJa0lUzInCC6gsBx7QMrS', 'dokter', '085173200421', '2026-06-08 07:07:45', '6a2688cbc39f7.png'),
(4, 'Vera Novana', 'veranovana@gmail.com', '$2y$10$fS9sqVBi3hcjyawufR07ceaXKI4lebjBw8yvNUNaohyHreBqoa6ny', 'pasien', '0811111111', '2026-06-11 05:45:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama_produk` varchar(150) NOT NULL,
  `kategori` enum('obat','vitamin','skincare','peralatan','lainnya') NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `url_gambar` varchar(255) DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama_produk`, `kategori`, `deskripsi`, `harga`, `stok`, `url_gambar`, `dibuat_pada`) VALUES
(1, 'Vitamin C 1000mg', '', 'Suplemen Daya Tahan', 45000.00, 100, '600x400.jpg', '2026-06-08 08:54:33'),
(2, 'Paracetamol 500mg', '', 'Pereda Demam', 15000.00, 100, '600x400.jpg', '2026-06-08 08:54:33'),
(4, 'Masker Medis 3Ply', '', 'Alat Kesehatan', 25000.00, 100, '6a26852f09dec.png', '2026-06-08 08:54:33'),
(5, 'Cup Sealer 1 Roll', '', '11111111', 45000.00, 0, '6a2688dc83fd1.png', '2026-06-08 09:03:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `janji_temu`
--
ALTER TABLE `janji_temu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_dokter` (`id_dokter`),
  ADD KEY `id_pasien` (`id_pasien`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `janji_temu`
--
ALTER TABLE `janji_temu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dokter`
--
ALTER TABLE `dokter`
  ADD CONSTRAINT `dokter_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `janji_temu`
--
ALTER TABLE `janji_temu`
  ADD CONSTRAINT `janji_temu_ibfk_1` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `janji_temu_ibfk_2` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pasien`
--
ALTER TABLE `pasien`
  ADD CONSTRAINT `pasien_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

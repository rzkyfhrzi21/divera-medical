-- Membuat Database
CREATE DATABASE IF NOT EXISTS `app_sip_kafe` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

-- Menggunakan Database
USE `app_sip_kafe`;

-- --------------------------------------------------------
-- Struktur Tabel: admin
-- --------------------------------------------------------

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data untuk tabel admin
INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama_lengkap`, `email`, `no_hp`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'ddyinnpalentin', 'delinpalentin02@gmail.com', '085840064684');

-- --------------------------------------------------------
-- Struktur Tabel: kafe
-- --------------------------------------------------------

CREATE TABLE `kafe` (
  `id_kafe` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kafe` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `harga_terendah` int(11) DEFAULT NULL,
  `harga_tertinggi` int(11) DEFAULT NULL,
  `foto_kafe` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_kafe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur Tabel: clustering
-- --------------------------------------------------------

CREATE TABLE `clustering` (
  `id_cluster` int(11) NOT NULL AUTO_INCREMENT,
  `nama_file` varchar(100) DEFAULT NULL,
  `jumlah_cluster` int(11) DEFAULT NULL,
  `jumlah_data` int(11) DEFAULT NULL,
  `waktu_clustering` datetime DEFAULT NULL,
  PRIMARY KEY (`id_cluster`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur Tabel: hasil_kuisioner
-- --------------------------------------------------------

CREATE TABLE `hasil_kuisioner` (
  `id_kuisioner` int(11) NOT NULL AUTO_INCREMENT,
  `id_kafe` int(11) DEFAULT NULL,
  `rasa_kopi` float DEFAULT NULL,
  `pelayanan` float DEFAULT NULL,
  `fasilitas` float DEFAULT NULL,
  `suasana` float DEFAULT NULL,
  `harga` float DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_kuisioner`),
  KEY `id_kafe` (`id_kafe`),
  CONSTRAINT `hasil_kuisioner_ibfk_1` 
    FOREIGN KEY (`id_kafe`) 
    REFERENCES `kafe` (`id_kafe`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur Tabel: hasil_clustering
-- --------------------------------------------------------

CREATE TABLE `hasil_clustering` (
  `id_hasil` int(11) NOT NULL AUTO_INCREMENT,
  `id_cluster` int(11) DEFAULT NULL,
  `id_kafe` int(11) DEFAULT NULL,
  `cluster` int(11) DEFAULT NULL,
  `jarak_centroid` float DEFAULT NULL,
  `peringkat_cluster` int(11) DEFAULT NULL,
  `rating_akhir` float NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_hasil`),
  KEY `id_cluster` (`id_cluster`),
  KEY `id_kafe` (`id_kafe`),
  CONSTRAINT `hasil_clustering_ibfk_1` 
    FOREIGN KEY (`id_cluster`) 
    REFERENCES `clustering` (`id_cluster`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
  CONSTRAINT `hasil_clustering_ibfk_2` 
    FOREIGN KEY (`id_kafe`) 
    REFERENCES `kafe` (`id_kafe`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 25, 2026 at 07:08 AM
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
-- Database: `app_sip_kafe`
--

-- --------------------------------------------------------

--
-- Table structure for table `hasil_wsm`
--

CREATE TABLE `hasil_wsm` (
  `id_wsm` int(11) NOT NULL,
  `nama_file` varchar(155) NOT NULL,
  `rasa_kopi` decimal(5,2) DEFAULT NULL,
  `pelayanan` decimal(5,2) DEFAULT NULL,
  `fasilitas` decimal(5,2) DEFAULT NULL,
  `suasana` decimal(5,2) DEFAULT NULL,
  `harga` decimal(5,2) DEFAULT NULL,
  `rating` decimal(5,2) DEFAULT NULL,
  `bobot_rasa` decimal(5,2) DEFAULT NULL,
  `bobot_pelayanan` decimal(5,2) DEFAULT NULL,
  `bobot_fasilitas` decimal(5,2) DEFAULT NULL,
  `bobot_suasana` decimal(5,2) DEFAULT NULL,
  `bobot_harga` decimal(5,2) DEFAULT NULL,
  `bobot_rating` decimal(5,2) DEFAULT NULL,
  `tgl_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hasil_wsm`
--
ALTER TABLE `hasil_wsm`
  ADD PRIMARY KEY (`id_wsm`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hasil_wsm`
--
ALTER TABLE `hasil_wsm`
  MODIFY `id_wsm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

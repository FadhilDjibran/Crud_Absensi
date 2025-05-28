-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2025 at 02:59 PM
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
-- Database: `absensi_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Hadir','Izin','Sakit','Alpha') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `nama`, `tanggal`, `status`) VALUES
(14, 'Fadhil Djibran', '2025-05-27', 'Hadir'),
(15, 'Nasikh Andhyka', '2025-05-27', 'Hadir'),
(16, 'Rafi Walidain', '2025-05-27', 'Hadir'),
(17, 'Syaifudin Afandi', '2025-05-27', 'Izin'),
(18, 'Reno Maulidyan', '2025-05-27', 'Sakit'),
(39, 'Fadhil Djibran', '2025-05-26', 'Sakit'),
(40, 'Nasikh Andhyka', '2025-05-26', 'Hadir'),
(41, 'Rafi Walidain', '2025-05-26', 'Izin'),
(42, 'Syaifudin Afandi', '2025-05-26', 'Alpha'),
(43, 'Reno Maulidyan', '2025-05-26', 'Hadir'),
(44, 'Fadhil Djibran', '2025-05-23', 'Hadir'),
(45, 'Nasikh Andhyka', '2025-05-23', 'Alpha'),
(46, 'Rafi Walidain', '2025-05-23', 'Hadir'),
(47, 'Syaifudin Afandi', '2025-05-23', 'Sakit'),
(48, 'Reno Maulidyan', '2025-05-23', 'Izin'),
(49, 'Fadhil Djibran', '2025-05-22', 'Izin'),
(50, 'Nasikh Andhyka', '2025-05-22', 'Hadir'),
(51, 'Rafi Walidain', '2025-05-22', 'Sakit'),
(52, 'Syaifudin Afandi', '2025-05-22', 'Hadir'),
(53, 'Reno Maulidyan', '2025-05-22', 'Alpha'),
(54, 'Fadhil Djibran', '2025-05-21', 'Alpha'),
(55, 'Nasikh Andhyka', '2025-05-21', 'Sakit'),
(56, 'Rafi Walidain', '2025-05-21', 'Hadir'),
(57, 'Syaifudin Afandi', '2025-05-21', 'Izin'),
(58, 'Reno Maulidyan', '2025-05-21', 'Hadir');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'karyawan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'Fadhil Djibran', '$2y$10$YEyGqaGSM0RKrKUumiYewOq200eW4ZuasAG5r99cgdqgKWv.a1xQ2', 'admin'),
(2, 'Nasikh Andhyka', '$2y$10$jEPpaX0UqTzqpqCzaSbi0uFy1ivCknoJcYy53QCtfuYvoCzyMSl3G', 'admin'),
(3, 'Syaifudin Afandi', '$2y$10$FBzNR9jK4m6Zl1vezXhCAOldTcx9R/lSBBFCTtkAdzfjqtr4XmIR2', 'karyawan'),
(4, 'Rafi Walidain', '$2y$10$.Uz85v5tb4Nn/HLwfzuGfOEkYGIfrx9k8MVayhOKOeI.7YTnBbFFW', 'karyawan'),
(5, 'Reno Maulidyan', '$2y$10$dimlzvc6g9kCRgUdzFfOnuvIEEyWHSwd3nBlYy3d14pmC3WdnMgUa', 'karyawan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

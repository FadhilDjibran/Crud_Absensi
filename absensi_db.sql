-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 02:59 PM
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
  `user_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `status` enum('Hadir','Izin','Sakit','Alpha') NOT NULL,
  `kondisi_masuk` enum('Tepat Waktu','Terlambat') DEFAULT NULL,
  `bukti_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `user_id`, `nama`, `tanggal`, `jam_masuk`, `status`, `kondisi_masuk`, `bukti_file`) VALUES
(385, 1, 'Fadhil Djibran', '2025-05-01', '07:55:01', 'Hadir', 'Tepat Waktu', NULL),
(386, 2, 'Nasikh Andhyka', '2025-05-01', '08:15:44', 'Hadir', 'Terlambat', NULL),
(387, 3, 'Syaifudin Afandi', '2025-05-01', '07:40:19', 'Hadir', 'Tepat Waktu', NULL),
(388, 4, 'Rafi Walidain', '2025-05-01', NULL, 'Izin', NULL, NULL),
(389, 5, 'Reno Maulidyan', '2025-05-01', '07:58:33', 'Hadir', 'Tepat Waktu', NULL),
(390, 1, 'Fadhil Djibran', '2025-05-02', '07:49:12', 'Hadir', 'Tepat Waktu', NULL),
(391, 2, 'Nasikh Andhyka', '2025-05-02', '07:51:05', 'Hadir', 'Tepat Waktu', NULL),
(392, 3, 'Syaifudin Afandi', '2025-05-02', '07:59:59', 'Hadir', 'Tepat Waktu', NULL),
(393, 4, 'Rafi Walidain', '2025-05-02', '08:05:21', 'Hadir', 'Terlambat', NULL),
(394, 5, 'Reno Maulidyan', '2025-05-02', NULL, 'Sakit', NULL, NULL),
(395, 1, 'Fadhil Djibran', '2025-05-05', '08:20:11', 'Hadir', 'Terlambat', NULL),
(396, 2, 'Nasikh Andhyka', '2025-05-05', '07:45:30', 'Hadir', 'Tepat Waktu', NULL),
(397, 3, 'Syaifudin Afandi', '2025-05-05', '07:56:09', 'Hadir', 'Tepat Waktu', NULL),
(398, 4, 'Rafi Walidain', '2025-05-05', '07:48:55', 'Hadir', 'Tepat Waktu', NULL),
(399, 5, 'Reno Maulidyan', '2025-05-05', '07:52:18', 'Hadir', 'Tepat Waktu', NULL),
(400, 1, 'Fadhil Djibran', '2025-05-06', '07:53:22', 'Hadir', 'Tepat Waktu', NULL),
(401, 2, 'Nasikh Andhyka', '2025-05-06', NULL, 'Alpha', NULL, NULL),
(402, 3, 'Syaifudin Afandi', '2025-05-06', '08:10:01', 'Hadir', 'Terlambat', NULL),
(403, 4, 'Rafi Walidain', '2025-05-06', '07:50:48', 'Hadir', 'Tepat Waktu', NULL),
(404, 5, 'Reno Maulidyan', '2025-05-06', '07:44:57', 'Hadir', 'Tepat Waktu', NULL),
(405, 1, 'Fadhil Djibran', '2025-05-07', '07:55:41', 'Hadir', 'Tepat Waktu', NULL),
(406, 2, 'Nasikh Andhyka', '2025-05-07', '07:58:14', 'Hadir', 'Tepat Waktu', NULL),
(407, 3, 'Syaifudin Afandi', '2025-05-07', NULL, 'Sakit', NULL, NULL),
(408, 4, 'Rafi Walidain', '2025-05-07', '07:47:03', 'Hadir', 'Tepat Waktu', NULL),
(409, 5, 'Reno Maulidyan', '2025-05-07', '08:30:15', 'Hadir', 'Terlambat', NULL),
(410, 1, 'Fadhil Djibran', '2025-05-08', '07:51:39', 'Hadir', 'Tepat Waktu', NULL),
(411, 2, 'Nasikh Andhyka', '2025-05-08', '07:42:58', 'Hadir', 'Tepat Waktu', NULL),
(412, 3, 'Syaifudin Afandi', '2025-05-08', '07:59:02', 'Hadir', 'Tepat Waktu', NULL),
(413, 4, 'Rafi Walidain', '2025-05-08', '07:49:19', 'Hadir', 'Tepat Waktu', NULL),
(414, 5, 'Reno Maulidyan', '2025-05-08', '07:55:08', 'Hadir', 'Tepat Waktu', NULL),
(415, 1, 'Fadhil Djibran', '2025-05-09', '08:01:50', 'Hadir', 'Terlambat', NULL),
(416, 2, 'Nasikh Andhyka', '2025-05-09', NULL, 'Izin', NULL, NULL),
(417, 3, 'Syaifudin Afandi', '2025-05-09', '07:46:11', 'Hadir', 'Tepat Waktu', NULL),
(418, 4, 'Rafi Walidain', '2025-05-09', '07:53:33', 'Hadir', 'Tepat Waktu', NULL),
(419, 5, 'Reno Maulidyan', '2025-05-09', '07:57:42', 'Hadir', 'Tepat Waktu', NULL),
(420, 1, 'Fadhil Djibran', '2025-06-02', '07:45:51', 'Hadir', 'Tepat Waktu', NULL),
(421, 2, 'Nasikh Andhyka', '2025-06-02', '07:56:13', 'Hadir', 'Tepat Waktu', NULL),
(422, 3, 'Syaifudin Afandi', '2025-06-02', '08:00:10', 'Hadir', 'Terlambat', NULL),
(423, 4, 'Rafi Walidain', '2025-06-02', '07:58:39', 'Hadir', 'Tepat Waktu', NULL),
(424, 5, 'Reno Maulidyan', '2025-06-02', NULL, 'Sakit', NULL, NULL),
(425, 1, 'Fadhil Djibran', '2025-06-03', '07:53:01', 'Hadir', 'Tepat Waktu', NULL),
(426, 2, 'Nasikh Andhyka', '2025-06-03', '07:49:55', 'Hadir', 'Tepat Waktu', NULL),
(427, 3, 'Syaifudin Afandi', '2025-06-03', '07:57:26', 'Hadir', 'Tepat Waktu', NULL),
(428, 4, 'Rafi Walidain', '2025-06-03', '07:55:40', 'Hadir', 'Tepat Waktu', NULL),
(429, 5, 'Reno Maulidyan', '2025-06-03', '08:04:19', 'Hadir', 'Terlambat', NULL),
(430, 1, 'Fadhil Djibran', '2025-06-04', '08:10:33', 'Hadir', 'Terlambat', NULL),
(431, 2, 'Nasikh Andhyka', '2025-06-04', '07:41:48', 'Hadir', 'Tepat Waktu', NULL),
(432, 3, 'Syaifudin Afandi', '2025-06-04', NULL, 'Izin', NULL, NULL),
(433, 4, 'Rafi Walidain', '2025-06-04', '07:59:58', 'Hadir', 'Tepat Waktu', NULL),
(434, 5, 'Reno Maulidyan', '2025-06-04', '07:46:07', 'Hadir', 'Tepat Waktu', NULL),
(435, 1, 'Fadhil Djibran', '2025-06-05', '07:52:50', 'Hadir', 'Tepat Waktu', NULL),
(436, 2, 'Nasikh Andhyka', '2025-06-05', '07:55:10', 'Hadir', 'Tepat Waktu', NULL),
(437, 3, 'Syaifudin Afandi', '2025-06-05', '08:00:00', 'Hadir', 'Tepat Waktu', NULL),
(438, 4, 'Rafi Walidain', '2025-06-05', '08:19:22', 'Hadir', 'Terlambat', NULL),
(439, 5, 'Reno Maulidyan', '2025-06-05', NULL, 'Alpha', NULL, NULL),
(440, 1, 'Fadhil Djibran', '2025-06-06', '07:56:31', 'Hadir', 'Tepat Waktu', NULL),
(441, 2, 'Nasikh Andhyka', '2025-06-06', '07:58:01', 'Hadir', 'Tepat Waktu', NULL),
(442, 3, 'Syaifudin Afandi', '2025-06-06', '07:51:25', 'Hadir', 'Tepat Waktu', NULL),
(443, 4, 'Rafi Walidain', '2025-06-06', '07:44:03', 'Hadir', 'Tepat Waktu', NULL),
(444, 5, 'Reno Maulidyan', '2025-06-06', '07:59:19', 'Hadir', 'Tepat Waktu', NULL),
(445, 1, 'Fadhil Djibran', '2025-06-09', '07:48:08', 'Hadir', 'Tepat Waktu', NULL),
(446, 2, 'Nasikh Andhyka', '2025-06-09', '08:21:49', 'Hadir', 'Terlambat', NULL),
(447, 3, 'Syaifudin Afandi', '2025-06-09', '07:55:55', 'Hadir', 'Tepat Waktu', NULL),
(448, 4, 'Rafi Walidain', '2025-06-09', '07:49:33', 'Hadir', 'Tepat Waktu', NULL),
(450, 1, 'Fadhil Djibran', '2025-05-12', '07:48:29', 'Hadir', 'Tepat Waktu', NULL),
(451, 2, 'Nasikh Andhyka', '2025-05-12', '07:50:17', 'Hadir', 'Tepat Waktu', NULL),
(452, 3, 'Syaifudin Afandi', '2025-05-12', '08:08:23', 'Hadir', 'Terlambat', NULL),
(453, 4, 'Rafi Walidain', '2025-05-12', '07:58:04', 'Hadir', 'Tepat Waktu', NULL),
(454, 5, 'Reno Maulidyan', '2025-05-12', NULL, 'Sakit', NULL, NULL),
(455, 1, 'Fadhil Djibran', '2025-05-13', '07:56:10', 'Hadir', 'Tepat Waktu', NULL),
(456, 2, 'Nasikh Andhyka', '2025-05-13', '07:54:19', 'Hadir', 'Tepat Waktu', NULL),
(457, 3, 'Syaifudin Afandi', '2025-05-13', '07:44:40', 'Hadir', 'Tepat Waktu', NULL),
(458, 4, 'Rafi Walidain', '2025-05-13', '07:52:12', 'Hadir', 'Tepat Waktu', NULL),
(459, 5, 'Reno Maulidyan', '2025-05-13', '08:02:44', 'Hadir', 'Terlambat', NULL),
(460, 1, 'Fadhil Djibran', '2025-05-14', NULL, 'Sakit', NULL, NULL),
(461, 2, 'Nasikh Andhyka', '2025-05-14', '07:43:08', 'Hadir', 'Tepat Waktu', NULL),
(462, 3, 'Syaifudin Afandi', '2025-05-14', '07:51:33', 'Hadir', 'Tepat Waktu', NULL),
(463, 4, 'Rafi Walidain', '2025-05-14', NULL, 'Alpha', NULL, NULL),
(464, 5, 'Reno Maulidyan', '2025-05-14', '07:59:16', 'Hadir', 'Tepat Waktu', NULL),
(465, 1, 'Fadhil Djibran', '2025-05-15', '07:57:48', 'Hadir', 'Tepat Waktu', NULL),
(466, 2, 'Nasikh Andhyka', '2025-05-15', '07:45:00', 'Hadir', 'Tepat Waktu', NULL),
(467, 3, 'Syaifudin Afandi', '2025-05-15', '07:53:59', 'Hadir', 'Tepat Waktu', NULL),
(468, 4, 'Rafi Walidain', '2025-05-15', '08:15:19', 'Hadir', 'Terlambat', NULL),
(469, 5, 'Reno Maulidyan', '2025-05-15', '07:50:52', 'Hadir', 'Tepat Waktu', NULL),
(470, 1, 'Fadhil Djibran', '2025-05-16', '07:41:20', 'Hadir', 'Tepat Waktu', NULL),
(471, 2, 'Nasikh Andhyka', '2025-05-16', '08:25:31', 'Hadir', 'Terlambat', NULL),
(472, 3, 'Syaifudin Afandi', '2025-05-16', '07:56:56', 'Hadir', 'Tepat Waktu', NULL),
(473, 4, 'Rafi Walidain', '2025-05-16', '07:48:10', 'Hadir', 'Tepat Waktu', NULL),
(474, 5, 'Reno Maulidyan', '2025-05-16', NULL, 'Izin', NULL, NULL),
(475, 1, 'Fadhil Djibran', '2025-05-19', '07:54:30', 'Hadir', 'Tepat Waktu', NULL),
(476, 2, 'Nasikh Andhyka', '2025-05-19', '07:47:22', 'Hadir', 'Tepat Waktu', NULL),
(477, 3, 'Syaifudin Afandi', '2025-05-19', '07:51:44', 'Hadir', 'Tepat Waktu', NULL),
(478, 4, 'Rafi Walidain', '2025-05-19', '07:59:11', 'Hadir', 'Tepat Waktu', NULL),
(479, 5, 'Reno Maulidyan', '2025-05-19', '08:00:30', 'Hadir', 'Terlambat', NULL),
(480, 1, 'Fadhil Djibran', '2025-05-20', '07:58:58', 'Hadir', 'Tepat Waktu', NULL),
(481, 2, 'Nasikh Andhyka', '2025-05-20', '08:03:17', 'Hadir', 'Terlambat', NULL),
(482, 3, 'Syaifudin Afandi', '2025-05-20', '07:45:05', 'Hadir', 'Tepat Waktu', NULL),
(483, 4, 'Rafi Walidain', '2025-05-20', '07:55:29', 'Hadir', 'Tepat Waktu', NULL),
(484, 5, 'Reno Maulidyan', '2025-05-20', NULL, 'Alpha', NULL, NULL),
(485, 1, 'Fadhil Djibran', '2025-05-21', '07:49:50', 'Hadir', 'Tepat Waktu', NULL),
(486, 2, 'Nasikh Andhyka', '2025-05-21', '07:52:43', 'Hadir', 'Tepat Waktu', NULL),
(487, 3, 'Syaifudin Afandi', '2025-05-21', '07:58:38', 'Hadir', 'Tepat Waktu', NULL),
(488, 4, 'Rafi Walidain', '2025-05-21', '08:01:01', 'Hadir', 'Terlambat', NULL),
(489, 5, 'Reno Maulidyan', '2025-05-21', NULL, 'Sakit', NULL, NULL),
(490, 1, 'Fadhil Djibran', '2025-05-22', '07:50:11', 'Hadir', 'Tepat Waktu', NULL),
(491, 2, 'Nasikh Andhyka', '2025-05-22', '07:44:19', 'Hadir', 'Tepat Waktu', NULL),
(492, 3, 'Syaifudin Afandi', '2025-05-22', '07:53:49', 'Hadir', 'Tepat Waktu', NULL),
(493, 4, 'Rafi Walidain', '2025-05-22', '07:59:34', 'Hadir', 'Tepat Waktu', NULL),
(494, 5, 'Reno Maulidyan', '2025-05-22', '07:42:06', 'Hadir', 'Tepat Waktu', NULL),
(495, 1, 'Fadhil Djibran', '2025-05-23', NULL, 'Izin', NULL, NULL),
(496, 2, 'Nasikh Andhyka', '2025-05-23', '07:58:20', 'Hadir', 'Tepat Waktu', NULL),
(497, 3, 'Syaifudin Afandi', '2025-05-23', '08:12:35', 'Hadir', 'Terlambat', NULL),
(498, 4, 'Rafi Walidain', '2025-05-23', '07:51:51', 'Hadir', 'Tepat Waktu', NULL),
(499, 5, 'Reno Maulidyan', '2025-05-23', '07:55:58', 'Hadir', 'Tepat Waktu', NULL),
(500, 1, 'Fadhil Djibran', '2025-05-26', '07:47:47', 'Hadir', 'Tepat Waktu', NULL),
(501, 2, 'Nasikh Andhyka', '2025-05-26', '08:00:00', 'Hadir', 'Tepat Waktu', NULL),
(502, 3, 'Syaifudin Afandi', '2025-05-26', '08:09:13', 'Hadir', 'Terlambat', NULL),
(503, 4, 'Rafi Walidain', '2025-05-26', '07:56:18', 'Hadir', 'Tepat Waktu', NULL),
(504, 5, 'Reno Maulidyan', '2025-05-26', '07:58:43', 'Hadir', 'Tepat Waktu', NULL),
(505, 1, 'Fadhil Djibran', '2025-05-27', '07:51:02', 'Hadir', 'Tepat Waktu', NULL),
(506, 2, 'Nasikh Andhyka', '2025-05-27', '07:48:36', 'Hadir', 'Tepat Waktu', NULL),
(507, 3, 'Syaifudin Afandi', '2025-05-27', NULL, 'Sakit', NULL, NULL),
(508, 4, 'Rafi Walidain', '2025-05-27', '07:53:07', 'Hadir', 'Tepat Waktu', NULL),
(509, 5, 'Reno Maulidyan', '2025-05-27', '07:50:24', 'Hadir', 'Tepat Waktu', NULL),
(510, 1, 'Fadhil Djibran', '2025-05-28', '08:05:55', 'Hadir', 'Terlambat', NULL),
(511, 2, 'Nasikh Andhyka', '2025-05-28', '07:57:33', 'Hadir', 'Tepat Waktu', NULL),
(512, 3, 'Syaifudin Afandi', '2025-05-28', '07:46:51', 'Hadir', 'Tepat Waktu', NULL),
(513, 4, 'Rafi Walidain', '2025-05-28', NULL, 'Alpha', NULL, NULL),
(514, 5, 'Reno Maulidyan', '2025-05-28', '07:54:12', 'Hadir', 'Tepat Waktu', NULL),
(515, 1, 'Fadhil Djibran', '2025-05-29', '07:49:03', 'Hadir', 'Tepat Waktu', NULL),
(516, 2, 'Nasikh Andhyka', '2025-05-29', '07:50:50', 'Hadir', 'Tepat Waktu', NULL),
(517, 3, 'Syaifudin Afandi', '2025-05-29', '08:11:09', 'Hadir', 'Terlambat', NULL),
(518, 4, 'Rafi Walidain', '2025-05-29', '07:52:37', 'Hadir', 'Tepat Waktu', NULL),
(519, 5, 'Reno Maulidyan', '2025-05-29', '07:58:28', 'Hadir', 'Tepat Waktu', NULL),
(520, 1, 'Fadhil Djibran', '2025-05-30', '07:55:18', 'Hadir', 'Tepat Waktu', NULL),
(521, 2, 'Nasikh Andhyka', '2025-05-30', NULL, 'Izin', NULL, NULL),
(522, 3, 'Syaifudin Afandi', '2025-05-30', '07:43:45', 'Hadir', 'Tepat Waktu', NULL),
(523, 4, 'Rafi Walidain', '2025-05-30', '08:22:14', 'Hadir', 'Terlambat', NULL),
(524, 5, 'Reno Maulidyan', '2025-05-30', '07:59:40', 'Hadir', 'Tepat Waktu', NULL),
(525, 5, 'Reno Maulidyan', '2025-06-09', NULL, 'Sakit', NULL, 'uploads/bukti_absensi/bukti_6846da70a1bbe4.68068645.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuanabsensi`
--

CREATE TABLE `pengajuanabsensi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `status_diajukan` enum('Hadir','Izin','Sakit') NOT NULL,
  `kondisi_masuk` enum('Tepat Waktu','Terlambat') DEFAULT NULL,
  `bukti_file` varchar(255) DEFAULT NULL,
  `status_review` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengajuanabsensi`
--

INSERT INTO `pengajuanabsensi` (`id`, `user_id`, `nama`, `tanggal`, `jam_masuk`, `status_diajukan`, `kondisi_masuk`, `bukti_file`, `status_review`, `created_at`) VALUES
(14, 5, 'Reno Maulidyan', '2025-06-09', NULL, 'Sakit', NULL, 'uploads/bukti_absensi/bukti_6846da70a1bbe4.68068645.jpg', 'disetujui', '2025-06-09 12:58:24');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_absensi_user` (`user_id`);

--
-- Indexes for table `pengajuanabsensi`
--
ALTER TABLE `pengajuanabsensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=526;

--
-- AUTO_INCREMENT for table `pengajuanabsensi`
--
ALTER TABLE `pengajuanabsensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `fk_absensi_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pengajuanabsensi`
--
ALTER TABLE `pengajuanabsensi`
  ADD CONSTRAINT `pengajuanabsensi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2025 at 12:21 PM
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
  `status` enum('Hadir','Izin','Sakit','Alpha') NOT NULL,
  `bukti_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `user_id`, `nama`, `tanggal`, `status`, `bukti_file`) VALUES
(65, 1, 'Fadhil Djibran', '2025-03-03', 'Hadir', NULL),
(66, 1, 'Fadhil Djibran', '2025-03-04', 'Hadir', NULL),
(67, 1, 'Fadhil Djibran', '2025-03-05', 'Sakit', NULL),
(68, 1, 'Fadhil Djibran', '2025-03-06', 'Hadir', NULL),
(69, 1, 'Fadhil Djibran', '2025-03-07', 'Hadir', NULL),
(70, 1, 'Fadhil Djibran', '2025-03-10', 'Hadir', NULL),
(71, 1, 'Fadhil Djibran', '2025-03-11', 'Izin', NULL),
(72, 1, 'Fadhil Djibran', '2025-03-12', 'Hadir', NULL),
(73, 1, 'Fadhil Djibran', '2025-03-13', 'Hadir', NULL),
(74, 1, 'Fadhil Djibran', '2025-03-14', 'Hadir', NULL),
(75, 1, 'Fadhil Djibran', '2025-03-17', 'Hadir', NULL),
(76, 1, 'Fadhil Djibran', '2025-03-18', 'Hadir', NULL),
(77, 1, 'Fadhil Djibran', '2025-03-19', 'Hadir', NULL),
(78, 1, 'Fadhil Djibran', '2025-03-20', 'Alpha', NULL),
(79, 1, 'Fadhil Djibran', '2025-03-21', 'Hadir', NULL),
(80, 1, 'Fadhil Djibran', '2025-03-24', 'Hadir', NULL),
(81, 1, 'Fadhil Djibran', '2025-03-25', 'Sakit', NULL),
(82, 1, 'Fadhil Djibran', '2025-03-26', 'Hadir', NULL),
(83, 1, 'Fadhil Djibran', '2025-03-27', 'Hadir', NULL),
(84, 1, 'Fadhil Djibran', '2025-03-28', 'Hadir', NULL),
(85, 1, 'Fadhil Djibran', '2025-03-31', 'Hadir', NULL),
(86, 1, 'Fadhil Djibran', '2025-04-01', 'Hadir', NULL),
(87, 1, 'Fadhil Djibran', '2025-04-02', 'Izin', NULL),
(88, 1, 'Fadhil Djibran', '2025-04-03', 'Hadir', NULL),
(89, 1, 'Fadhil Djibran', '2025-04-04', 'Hadir', NULL),
(90, 1, 'Fadhil Djibran', '2025-04-07', 'Hadir', NULL),
(91, 1, 'Fadhil Djibran', '2025-04-08', 'Hadir', NULL),
(92, 1, 'Fadhil Djibran', '2025-04-09', 'Hadir', NULL),
(93, 1, 'Fadhil Djibran', '2025-04-10', 'Sakit', NULL),
(94, 1, 'Fadhil Djibran', '2025-04-11', 'Hadir', NULL),
(95, 1, 'Fadhil Djibran', '2025-04-14', 'Hadir', NULL),
(96, 1, 'Fadhil Djibran', '2025-04-15', 'Hadir', NULL),
(97, 1, 'Fadhil Djibran', '2025-04-16', 'Alpha', NULL),
(98, 1, 'Fadhil Djibran', '2025-04-17', 'Hadir', NULL),
(99, 1, 'Fadhil Djibran', '2025-04-18', 'Hadir', NULL),
(100, 1, 'Fadhil Djibran', '2025-04-21', 'Hadir', NULL),
(101, 1, 'Fadhil Djibran', '2025-04-22', 'Hadir', NULL),
(102, 1, 'Fadhil Djibran', '2025-04-23', 'Hadir', NULL),
(103, 1, 'Fadhil Djibran', '2025-04-24', 'Izin', NULL),
(104, 1, 'Fadhil Djibran', '2025-04-25', 'Hadir', NULL),
(105, 1, 'Fadhil Djibran', '2025-04-28', 'Hadir', NULL),
(106, 1, 'Fadhil Djibran', '2025-04-29', 'Sakit', NULL),
(107, 1, 'Fadhil Djibran', '2025-04-30', 'Hadir', NULL),
(108, 1, 'Fadhil Djibran', '2025-05-01', 'Hadir', NULL),
(109, 1, 'Fadhil Djibran', '2025-05-02', 'Hadir', NULL),
(110, 1, 'Fadhil Djibran', '2025-05-05', 'Hadir', NULL),
(111, 1, 'Fadhil Djibran', '2025-05-06', 'Hadir', NULL),
(112, 1, 'Fadhil Djibran', '2025-05-07', 'Hadir', NULL),
(113, 1, 'Fadhil Djibran', '2025-05-08', 'Alpha', NULL),
(114, 1, 'Fadhil Djibran', '2025-05-09', 'Hadir', NULL),
(115, 1, 'Fadhil Djibran', '2025-05-12', 'Hadir', NULL),
(116, 1, 'Fadhil Djibran', '2025-05-13', 'Izin', NULL),
(117, 1, 'Fadhil Djibran', '2025-05-14', 'Hadir', NULL),
(118, 1, 'Fadhil Djibran', '2025-05-15', 'Hadir', NULL),
(119, 1, 'Fadhil Djibran', '2025-05-16', 'Hadir', NULL),
(120, 1, 'Fadhil Djibran', '2025-05-19', 'Sakit', NULL),
(121, 1, 'Fadhil Djibran', '2025-05-20', 'Hadir', NULL),
(122, 1, 'Fadhil Djibran', '2025-05-21', 'Hadir', NULL),
(123, 1, 'Fadhil Djibran', '2025-05-22', 'Hadir', NULL),
(124, 1, 'Fadhil Djibran', '2025-05-23', 'Hadir', NULL),
(125, 1, 'Fadhil Djibran', '2025-05-26', 'Hadir', NULL),
(126, 2, 'Nasikh Andhyka', '2025-03-03', 'Hadir', NULL),
(127, 2, 'Nasikh Andhyka', '2025-03-04', 'Hadir', NULL),
(128, 2, 'Nasikh Andhyka', '2025-03-05', 'Hadir', NULL),
(129, 2, 'Nasikh Andhyka', '2025-03-06', 'Izin', NULL),
(130, 2, 'Nasikh Andhyka', '2025-03-07', 'Hadir', NULL),
(131, 2, 'Nasikh Andhyka', '2025-03-10', 'Sakit', NULL),
(132, 2, 'Nasikh Andhyka', '2025-03-11', 'Hadir', NULL),
(133, 2, 'Nasikh Andhyka', '2025-03-12', 'Hadir', NULL),
(134, 2, 'Nasikh Andhyka', '2025-03-13', 'Hadir', NULL),
(135, 2, 'Nasikh Andhyka', '2025-03-14', 'Alpha', NULL),
(136, 2, 'Nasikh Andhyka', '2025-03-17', 'Hadir', NULL),
(137, 2, 'Nasikh Andhyka', '2025-03-18', 'Hadir', NULL),
(138, 2, 'Nasikh Andhyka', '2025-03-19', 'Hadir', NULL),
(139, 2, 'Nasikh Andhyka', '2025-03-20', 'Hadir', NULL),
(140, 2, 'Nasikh Andhyka', '2025-03-21', 'Izin', NULL),
(141, 2, 'Nasikh Andhyka', '2025-03-24', 'Hadir', NULL),
(142, 2, 'Nasikh Andhyka', '2025-03-25', 'Hadir', NULL),
(143, 2, 'Nasikh Andhyka', '2025-03-26', 'Sakit', NULL),
(144, 2, 'Nasikh Andhyka', '2025-03-27', 'Hadir', NULL),
(145, 2, 'Nasikh Andhyka', '2025-03-28', 'Hadir', NULL),
(146, 2, 'Nasikh Andhyka', '2025-03-31', 'Hadir', NULL),
(147, 2, 'Nasikh Andhyka', '2025-04-01', 'Hadir', NULL),
(148, 2, 'Nasikh Andhyka', '2025-04-02', 'Hadir', NULL),
(149, 2, 'Nasikh Andhyka', '2025-04-03', 'Alpha', NULL),
(150, 2, 'Nasikh Andhyka', '2025-04-04', 'Hadir', NULL),
(151, 2, 'Nasikh Andhyka', '2025-04-07', 'Hadir', NULL),
(152, 2, 'Nasikh Andhyka', '2025-04-08', 'Izin', NULL),
(153, 2, 'Nasikh Andhyka', '2025-04-09', 'Hadir', NULL),
(154, 2, 'Nasikh Andhyka', '2025-04-10', 'Hadir', NULL),
(155, 2, 'Nasikh Andhyka', '2025-04-11', 'Sakit', NULL),
(156, 2, 'Nasikh Andhyka', '2025-04-14', 'Hadir', NULL),
(157, 2, 'Nasikh Andhyka', '2025-04-15', 'Hadir', NULL),
(158, 2, 'Nasikh Andhyka', '2025-04-16', 'Hadir', NULL),
(159, 2, 'Nasikh Andhyka', '2025-04-17', 'Hadir', NULL),
(160, 2, 'Nasikh Andhyka', '2025-04-18', 'Hadir', NULL),
(161, 2, 'Nasikh Andhyka', '2025-04-21', 'Alpha', NULL),
(162, 2, 'Nasikh Andhyka', '2025-04-22', 'Hadir', NULL),
(163, 2, 'Nasikh Andhyka', '2025-04-23', 'Hadir', NULL),
(164, 2, 'Nasikh Andhyka', '2025-04-24', 'Hadir', NULL),
(165, 2, 'Nasikh Andhyka', '2025-04-25', 'Izin', NULL),
(166, 2, 'Nasikh Andhyka', '2025-04-28', 'Hadir', NULL),
(167, 2, 'Nasikh Andhyka', '2025-04-29', 'Hadir', NULL),
(168, 2, 'Nasikh Andhyka', '2025-04-30', 'Sakit', NULL),
(169, 2, 'Nasikh Andhyka', '2025-05-01', 'Hadir', NULL),
(170, 2, 'Nasikh Andhyka', '2025-05-02', 'Hadir', NULL),
(171, 2, 'Nasikh Andhyka', '2025-05-05', 'Hadir', NULL),
(172, 2, 'Nasikh Andhyka', '2025-05-06', 'Hadir', NULL),
(173, 2, 'Nasikh Andhyka', '2025-05-07', 'Alpha', NULL),
(174, 2, 'Nasikh Andhyka', '2025-05-08', 'Hadir', NULL),
(175, 2, 'Nasikh Andhyka', '2025-05-09', 'Hadir', NULL),
(176, 2, 'Nasikh Andhyka', '2025-05-12', 'Hadir', NULL),
(177, 2, 'Nasikh Andhyka', '2025-05-13', 'Hadir', NULL),
(178, 2, 'Nasikh Andhyka', '2025-05-14', 'Izin', NULL),
(179, 2, 'Nasikh Andhyka', '2025-05-15', 'Hadir', NULL),
(180, 2, 'Nasikh Andhyka', '2025-05-16', 'Sakit', NULL),
(181, 2, 'Nasikh Andhyka', '2025-05-19', 'Hadir', NULL),
(182, 2, 'Nasikh Andhyka', '2025-05-20', 'Hadir', NULL),
(183, 2, 'Nasikh Andhyka', '2025-05-21', 'Hadir', NULL),
(184, 2, 'Nasikh Andhyka', '2025-05-22', 'Hadir', NULL),
(185, 2, 'Nasikh Andhyka', '2025-05-23', 'Hadir', NULL),
(186, 2, 'Nasikh Andhyka', '2025-05-26', 'Hadir', NULL),
(187, 3, 'Syaifudin Afandi', '2025-03-03', 'Hadir', NULL),
(188, 3, 'Syaifudin Afandi', '2025-03-04', 'Izin', NULL),
(189, 3, 'Syaifudin Afandi', '2025-03-05', 'Hadir', NULL),
(190, 3, 'Syaifudin Afandi', '2025-03-06', 'Hadir', NULL),
(191, 3, 'Syaifudin Afandi', '2025-03-07', 'Sakit', NULL),
(192, 3, 'Syaifudin Afandi', '2025-03-10', 'Hadir', NULL),
(193, 3, 'Syaifudin Afandi', '2025-03-11', 'Hadir', NULL),
(194, 3, 'Syaifudin Afandi', '2025-03-12', 'Alpha', NULL),
(195, 3, 'Syaifudin Afandi', '2025-03-13', 'Hadir', NULL),
(196, 3, 'Syaifudin Afandi', '2025-03-14', 'Hadir', NULL),
(197, 3, 'Syaifudin Afandi', '2025-03-17', 'Hadir', NULL),
(198, 3, 'Syaifudin Afandi', '2025-03-18', 'Hadir', NULL),
(199, 3, 'Syaifudin Afandi', '2025-03-19', 'Izin', NULL),
(200, 3, 'Syaifudin Afandi', '2025-03-20', 'Hadir', NULL),
(201, 3, 'Syaifudin Afandi', '2025-03-21', 'Sakit', NULL),
(202, 3, 'Syaifudin Afandi', '2025-03-24', 'Hadir', NULL),
(203, 3, 'Syaifudin Afandi', '2025-03-25', 'Hadir', NULL),
(204, 3, 'Syaifudin Afandi', '2025-03-26', 'Hadir', NULL),
(205, 3, 'Syaifudin Afandi', '2025-03-27', 'Alpha', NULL),
(206, 3, 'Syaifudin Afandi', '2025-03-28', 'Hadir', NULL),
(207, 3, 'Syaifudin Afandi', '2025-03-31', 'Hadir', NULL),
(208, 3, 'Syaifudin Afandi', '2025-04-01', 'Hadir', NULL),
(209, 3, 'Syaifudin Afandi', '2025-04-02', 'Hadir', NULL),
(210, 3, 'Syaifudin Afandi', '2025-04-03', 'Sakit', NULL),
(211, 3, 'Syaifudin Afandi', '2025-04-04', 'Hadir', NULL),
(212, 3, 'Syaifudin Afandi', '2025-04-07', 'Izin', NULL),
(213, 3, 'Syaifudin Afandi', '2025-04-08', 'Hadir', NULL),
(214, 3, 'Syaifudin Afandi', '2025-04-09', 'Hadir', NULL),
(215, 3, 'Syaifudin Afandi', '2025-04-10', 'Hadir', NULL),
(216, 3, 'Syaifudin Afandi', '2025-04-11', 'Alpha', NULL),
(217, 3, 'Syaifudin Afandi', '2025-04-14', 'Hadir', NULL),
(218, 3, 'Syaifudin Afandi', '2025-04-15', 'Hadir', NULL),
(219, 3, 'Syaifudin Afandi', '2025-04-16', 'Hadir', NULL),
(220, 3, 'Syaifudin Afandi', '2025-04-17', 'Sakit', NULL),
(221, 3, 'Syaifudin Afandi', '2025-04-18', 'Hadir', NULL),
(222, 3, 'Syaifudin Afandi', '2025-04-21', 'Hadir', NULL),
(223, 3, 'Syaifudin Afandi', '2025-04-22', 'Izin', NULL),
(224, 3, 'Syaifudin Afandi', '2025-04-23', 'Hadir', NULL),
(225, 3, 'Syaifudin Afandi', '2025-04-24', 'Hadir', NULL),
(226, 3, 'Syaifudin Afandi', '2025-04-25', 'Hadir', NULL),
(227, 3, 'Syaifudin Afandi', '2025-04-28', 'Alpha', NULL),
(228, 3, 'Syaifudin Afandi', '2025-04-29', 'Hadir', NULL),
(229, 3, 'Syaifudin Afandi', '2025-04-30', 'Hadir', NULL),
(230, 3, 'Syaifudin Afandi', '2025-05-01', 'Hadir', NULL),
(231, 3, 'Syaifudin Afandi', '2025-05-02', 'Sakit', NULL),
(232, 3, 'Syaifudin Afandi', '2025-05-05', 'Hadir', NULL),
(233, 3, 'Syaifudin Afandi', '2025-05-06', 'Hadir', NULL),
(234, 3, 'Syaifudin Afandi', '2025-05-07', 'Hadir', NULL),
(235, 3, 'Syaifudin Afandi', '2025-05-08', 'Izin', NULL),
(236, 3, 'Syaifudin Afandi', '2025-05-09', 'Hadir', NULL),
(237, 3, 'Syaifudin Afandi', '2025-05-12', 'Hadir', NULL),
(238, 3, 'Syaifudin Afandi', '2025-05-13', 'Alpha', NULL),
(239, 3, 'Syaifudin Afandi', '2025-05-14', 'Hadir', NULL),
(240, 3, 'Syaifudin Afandi', '2025-05-15', 'Hadir', NULL),
(241, 3, 'Syaifudin Afandi', '2025-05-16', 'Hadir', NULL),
(242, 3, 'Syaifudin Afandi', '2025-05-19', 'Hadir', NULL),
(243, 3, 'Syaifudin Afandi', '2025-05-20', 'Sakit', NULL),
(244, 3, 'Syaifudin Afandi', '2025-05-21', 'Hadir', NULL),
(245, 3, 'Syaifudin Afandi', '2025-05-22', 'Hadir', NULL),
(246, 3, 'Syaifudin Afandi', '2025-05-23', 'Izin', NULL),
(247, 3, 'Syaifudin Afandi', '2025-05-26', 'Hadir', NULL),
(248, 4, 'Rafi Walidain', '2025-03-03', 'Alpha', NULL),
(249, 4, 'Rafi Walidain', '2025-03-04', 'Hadir', NULL),
(250, 4, 'Rafi Walidain', '2025-03-05', 'Hadir', NULL),
(251, 4, 'Rafi Walidain', '2025-03-06', 'Sakit', NULL),
(252, 4, 'Rafi Walidain', '2025-03-07', 'Hadir', NULL),
(253, 4, 'Rafi Walidain', '2025-03-10', 'Izin', NULL),
(254, 4, 'Rafi Walidain', '2025-03-11', 'Hadir', NULL),
(255, 4, 'Rafi Walidain', '2025-03-12', 'Hadir', NULL),
(256, 4, 'Rafi Walidain', '2025-03-13', 'Hadir', NULL),
(257, 4, 'Rafi Walidain', '2025-03-14', 'Hadir', NULL),
(258, 4, 'Rafi Walidain', '2025-03-17', 'Alpha', NULL),
(259, 4, 'Rafi Walidain', '2025-03-18', 'Hadir', NULL),
(260, 4, 'Rafi Walidain', '2025-03-19', 'Hadir', NULL),
(261, 4, 'Rafi Walidain', '2025-03-20', 'Sakit', NULL),
(262, 4, 'Rafi Walidain', '2025-03-21', 'Hadir', NULL),
(263, 4, 'Rafi Walidain', '2025-03-24', 'Izin', NULL),
(264, 4, 'Rafi Walidain', '2025-03-25', 'Hadir', NULL),
(265, 4, 'Rafi Walidain', '2025-03-26', 'Hadir', NULL),
(266, 4, 'Rafi Walidain', '2025-03-27', 'Hadir', NULL),
(267, 4, 'Rafi Walidain', '2025-03-28', 'Hadir', NULL),
(268, 4, 'Rafi Walidain', '2025-03-31', 'Alpha', NULL),
(269, 4, 'Rafi Walidain', '2025-04-01', 'Hadir', NULL),
(270, 4, 'Rafi Walidain', '2025-04-02', 'Sakit', NULL),
(271, 4, 'Rafi Walidain', '2025-04-03', 'Hadir', NULL),
(272, 4, 'Rafi Walidain', '2025-04-04', 'Izin', NULL),
(273, 4, 'Rafi Walidain', '2025-04-07', 'Hadir', NULL),
(274, 4, 'Rafi Walidain', '2025-04-08', 'Hadir', NULL),
(275, 4, 'Rafi Walidain', '2025-04-09', 'Hadir', NULL),
(276, 4, 'Rafi Walidain', '2025-04-10', 'Hadir', NULL),
(277, 4, 'Rafi Walidain', '2025-04-11', 'Alpha', NULL),
(278, 4, 'Rafi Walidain', '2025-04-14', 'Hadir', NULL),
(279, 4, 'Rafi Walidain', '2025-04-15', 'Sakit', NULL),
(280, 4, 'Rafi Walidain', '2025-04-16', 'Hadir', NULL),
(281, 4, 'Rafi Walidain', '2025-04-17', 'Izin', NULL),
(282, 4, 'Rafi Walidain', '2025-04-18', 'Hadir', NULL),
(283, 4, 'Rafi Walidain', '2025-04-21', 'Hadir', NULL),
(284, 4, 'Rafi Walidain', '2025-04-22', 'Hadir', NULL),
(285, 4, 'Rafi Walidain', '2025-04-23', 'Hadir', NULL),
(286, 4, 'Rafi Walidain', '2025-04-24', 'Alpha', NULL),
(287, 4, 'Rafi Walidain', '2025-04-25', 'Hadir', NULL),
(288, 4, 'Rafi Walidain', '2025-04-28', 'Sakit', NULL),
(289, 4, 'Rafi Walidain', '2025-04-29', 'Hadir', NULL),
(290, 4, 'Rafi Walidain', '2025-04-30', 'Izin', NULL),
(291, 4, 'Rafi Walidain', '2025-05-01', 'Hadir', NULL),
(292, 4, 'Rafi Walidain', '2025-05-02', 'Hadir', NULL),
(293, 4, 'Rafi Walidain', '2025-05-05', 'Hadir', NULL),
(294, 4, 'Rafi Walidain', '2025-05-06', 'Alpha', NULL),
(295, 4, 'Rafi Walidain', '2025-05-07', 'Hadir', NULL),
(296, 4, 'Rafi Walidain', '2025-05-08', 'Sakit', NULL),
(297, 4, 'Rafi Walidain', '2025-05-09', 'Hadir', NULL),
(298, 4, 'Rafi Walidain', '2025-05-12', 'Izin', NULL),
(299, 4, 'Rafi Walidain', '2025-05-13', 'Hadir', NULL),
(300, 4, 'Rafi Walidain', '2025-05-14', 'Hadir', NULL),
(301, 4, 'Rafi Walidain', '2025-05-15', 'Hadir', NULL),
(302, 4, 'Rafi Walidain', '2025-05-16', 'Hadir', NULL),
(303, 4, 'Rafi Walidain', '2025-05-19', 'Alpha', NULL),
(304, 4, 'Rafi Walidain', '2025-05-20', 'Hadir', NULL),
(305, 4, 'Rafi Walidain', '2025-05-21', 'Sakit', NULL),
(306, 4, 'Rafi Walidain', '2025-05-22', 'Hadir', NULL),
(307, 4, 'Rafi Walidain', '2025-05-23', 'Hadir', NULL),
(308, 4, 'Rafi Walidain', '2025-05-26', 'Izin', NULL),
(309, 5, 'Reno Maulidyan', '2025-03-03', 'Hadir', NULL),
(310, 5, 'Reno Maulidyan', '2025-03-04', 'Hadir', NULL),
(311, 5, 'Reno Maulidyan', '2025-03-05', 'Hadir', NULL),
(312, 5, 'Reno Maulidyan', '2025-03-06', 'Hadir', NULL),
(313, 5, 'Reno Maulidyan', '2025-03-07', 'Alpha', NULL),
(314, 5, 'Reno Maulidyan', '2025-03-10', 'Hadir', NULL),
(315, 5, 'Reno Maulidyan', '2025-03-11', 'Sakit', NULL),
(316, 5, 'Reno Maulidyan', '2025-03-12', 'Hadir', NULL),
(317, 5, 'Reno Maulidyan', '2025-03-13', 'Izin', NULL),
(318, 5, 'Reno Maulidyan', '2025-03-14', 'Hadir', NULL),
(319, 5, 'Reno Maulidyan', '2025-03-17', 'Hadir', NULL),
(320, 5, 'Reno Maulidyan', '2025-03-18', 'Hadir', NULL),
(321, 5, 'Reno Maulidyan', '2025-03-19', 'Alpha', NULL),
(322, 5, 'Reno Maulidyan', '2025-03-20', 'Hadir', NULL),
(323, 5, 'Reno Maulidyan', '2025-03-21', 'Hadir', NULL),
(324, 5, 'Reno Maulidyan', '2025-03-24', 'Sakit', NULL),
(325, 5, 'Reno Maulidyan', '2025-03-25', 'Hadir', NULL),
(326, 5, 'Reno Maulidyan', '2025-03-26', 'Izin', NULL),
(327, 5, 'Reno Maulidyan', '2025-03-27', 'Hadir', NULL),
(328, 5, 'Reno Maulidyan', '2025-03-28', 'Hadir', NULL),
(329, 5, 'Reno Maulidyan', '2025-03-31', 'Hadir', NULL),
(330, 5, 'Reno Maulidyan', '2025-04-01', 'Hadir', NULL),
(331, 5, 'Reno Maulidyan', '2025-04-02', 'Alpha', NULL),
(332, 5, 'Reno Maulidyan', '2025-04-03', 'Hadir', NULL),
(333, 5, 'Reno Maulidyan', '2025-04-04', 'Sakit', NULL),
(334, 5, 'Reno Maulidyan', '2025-04-07', 'Hadir', NULL),
(335, 5, 'Reno Maulidyan', '2025-04-08', 'Hadir', NULL),
(336, 5, 'Reno Maulidyan', '2025-04-09', 'Izin', NULL),
(337, 5, 'Reno Maulidyan', '2025-04-10', 'Hadir', NULL),
(338, 5, 'Reno Maulidyan', '2025-04-11', 'Hadir', NULL),
(339, 5, 'Reno Maulidyan', '2025-04-14', 'Hadir', NULL),
(340, 5, 'Reno Maulidyan', '2025-04-15', 'Alpha', NULL),
(341, 5, 'Reno Maulidyan', '2025-04-16', 'Hadir', NULL),
(342, 5, 'Reno Maulidyan', '2025-04-17', 'Sakit', NULL),
(343, 5, 'Reno Maulidyan', '2025-04-18', 'Hadir', NULL),
(344, 5, 'Reno Maulidyan', '2025-04-21', 'Izin', NULL),
(345, 5, 'Reno Maulidyan', '2025-04-22', 'Hadir', NULL),
(346, 5, 'Reno Maulidyan', '2025-04-23', 'Hadir', NULL),
(347, 5, 'Reno Maulidyan', '2025-04-24', 'Hadir', NULL),
(348, 5, 'Reno Maulidyan', '2025-04-25', 'Hadir', NULL),
(349, 5, 'Reno Maulidyan', '2025-04-28', 'Hadir', NULL),
(350, 5, 'Reno Maulidyan', '2025-04-29', 'Alpha', NULL),
(351, 5, 'Reno Maulidyan', '2025-04-30', 'Sakit', NULL),
(352, 5, 'Reno Maulidyan', '2025-05-01', 'Hadir', NULL),
(353, 5, 'Reno Maulidyan', '2025-05-02', 'Izin', NULL),
(354, 5, 'Reno Maulidyan', '2025-05-05', 'Hadir', NULL),
(355, 5, 'Reno Maulidyan', '2025-05-06', 'Hadir', NULL),
(356, 5, 'Reno Maulidyan', '2025-05-07', 'Hadir', NULL),
(357, 5, 'Reno Maulidyan', '2025-05-08', 'Hadir', NULL),
(358, 5, 'Reno Maulidyan', '2025-05-09', 'Alpha', NULL),
(359, 5, 'Reno Maulidyan', '2025-05-12', 'Hadir', NULL),
(360, 5, 'Reno Maulidyan', '2025-05-13', 'Sakit', NULL),
(361, 5, 'Reno Maulidyan', '2025-05-14', 'Hadir', NULL),
(362, 5, 'Reno Maulidyan', '2025-05-15', 'Izin', NULL),
(363, 5, 'Reno Maulidyan', '2025-05-16', 'Hadir', NULL),
(364, 5, 'Reno Maulidyan', '2025-05-19', 'Hadir', NULL),
(365, 5, 'Reno Maulidyan', '2025-05-20', 'Hadir', NULL),
(366, 5, 'Reno Maulidyan', '2025-05-21', 'Hadir', NULL),
(367, 5, 'Reno Maulidyan', '2025-05-22', 'Alpha', NULL),
(368, 5, 'Reno Maulidyan', '2025-05-23', 'Sakit', NULL),
(369, 5, 'Reno Maulidyan', '2025-05-26', 'Hadir', NULL),
(377, 3, 'Syaifudin Afandi', '2025-05-28', 'Izin', NULL),
(378, 4, 'Rafi Walidain', '2025-06-08', 'Sakit', NULL),
(379, 5, 'Reno Maulidyan', '2025-06-08', 'Sakit', NULL),
(380, 2, 'Nasikh Andhyka', '2025-06-08', 'Hadir', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pengajuanabsensi`
--

CREATE TABLE `pengajuanabsensi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `status_diajukan` enum('Hadir','Izin','Sakit') NOT NULL,
  `bukti_file` varchar(255) DEFAULT NULL,
  `status_review` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengajuanabsensi`
--

INSERT INTO `pengajuanabsensi` (`id`, `user_id`, `nama`, `tanggal`, `status_diajukan`, `bukti_file`, `status_review`, `created_at`) VALUES
(1, 3, 'Syaifudin Afandi', '2025-05-28', 'Hadir', NULL, 'ditolak', '2025-05-28 03:25:28'),
(2, 3, 'Syaifudin Afandi', '2025-05-28', 'Hadir', NULL, 'disetujui', '2025-05-28 03:32:31'),
(3, 3, 'Syaifudin Afandi', '2025-05-28', 'Izin', 'uploads/bukti_absensi/bukti_6836840522fb27.83165298.png', 'disetujui', '2025-05-28 03:33:25'),
(4, 3, 'Syaifudin Afandi', '2025-05-28', 'Izin', 'uploads/bukti_absensi/bukti_6836884c779620.34448505.png', 'disetujui', '2025-05-28 03:51:40'),
(5, 4, 'Rafi Walidain', '2025-05-28', 'Sakit', 'uploads/bukti_absensi/bukti_683688684e3402.15685370.png', 'ditolak', '2025-05-28 03:52:08'),
(6, 5, 'Reno Maulidyan', '2025-05-28', 'Hadir', NULL, 'ditolak', '2025-05-28 03:52:20'),
(7, 4, 'Rafi Walidain', '2025-06-08', 'Sakit', 'uploads/bukti_absensi/bukti_68456224d4f681.22668455.jpg', 'disetujui', '2025-06-08 10:12:52'),
(8, 5, 'Reno Maulidyan', '2025-06-08', 'Sakit', 'uploads/bukti_absensi/bukti_684562d522e9a4.00131242.jpg', 'disetujui', '2025-06-08 10:15:49');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=381;

--
-- AUTO_INCREMENT for table `pengajuanabsensi`
--
ALTER TABLE `pengajuanabsensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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

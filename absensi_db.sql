-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2025 at 05:39 AM
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
  `status` enum('Hadir','Izin','Sakit','Alpha') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `user_id`, `nama`, `tanggal`, `status`) VALUES
(65, 1, 'Fadhil Djibran', '2025-03-03', 'Hadir'),
(66, 1, 'Fadhil Djibran', '2025-03-04', 'Hadir'),
(67, 1, 'Fadhil Djibran', '2025-03-05', 'Sakit'),
(68, 1, 'Fadhil Djibran', '2025-03-06', 'Hadir'),
(69, 1, 'Fadhil Djibran', '2025-03-07', 'Hadir'),
(70, 1, 'Fadhil Djibran', '2025-03-10', 'Hadir'),
(71, 1, 'Fadhil Djibran', '2025-03-11', 'Izin'),
(72, 1, 'Fadhil Djibran', '2025-03-12', 'Hadir'),
(73, 1, 'Fadhil Djibran', '2025-03-13', 'Hadir'),
(74, 1, 'Fadhil Djibran', '2025-03-14', 'Hadir'),
(75, 1, 'Fadhil Djibran', '2025-03-17', 'Hadir'),
(76, 1, 'Fadhil Djibran', '2025-03-18', 'Hadir'),
(77, 1, 'Fadhil Djibran', '2025-03-19', 'Hadir'),
(78, 1, 'Fadhil Djibran', '2025-03-20', 'Alpha'),
(79, 1, 'Fadhil Djibran', '2025-03-21', 'Hadir'),
(80, 1, 'Fadhil Djibran', '2025-03-24', 'Hadir'),
(81, 1, 'Fadhil Djibran', '2025-03-25', 'Sakit'),
(82, 1, 'Fadhil Djibran', '2025-03-26', 'Hadir'),
(83, 1, 'Fadhil Djibran', '2025-03-27', 'Hadir'),
(84, 1, 'Fadhil Djibran', '2025-03-28', 'Hadir'),
(85, 1, 'Fadhil Djibran', '2025-03-31', 'Hadir'),
(86, 1, 'Fadhil Djibran', '2025-04-01', 'Hadir'),
(87, 1, 'Fadhil Djibran', '2025-04-02', 'Izin'),
(88, 1, 'Fadhil Djibran', '2025-04-03', 'Hadir'),
(89, 1, 'Fadhil Djibran', '2025-04-04', 'Hadir'),
(90, 1, 'Fadhil Djibran', '2025-04-07', 'Hadir'),
(91, 1, 'Fadhil Djibran', '2025-04-08', 'Hadir'),
(92, 1, 'Fadhil Djibran', '2025-04-09', 'Hadir'),
(93, 1, 'Fadhil Djibran', '2025-04-10', 'Sakit'),
(94, 1, 'Fadhil Djibran', '2025-04-11', 'Hadir'),
(95, 1, 'Fadhil Djibran', '2025-04-14', 'Hadir'),
(96, 1, 'Fadhil Djibran', '2025-04-15', 'Hadir'),
(97, 1, 'Fadhil Djibran', '2025-04-16', 'Alpha'),
(98, 1, 'Fadhil Djibran', '2025-04-17', 'Hadir'),
(99, 1, 'Fadhil Djibran', '2025-04-18', 'Hadir'),
(100, 1, 'Fadhil Djibran', '2025-04-21', 'Hadir'),
(101, 1, 'Fadhil Djibran', '2025-04-22', 'Hadir'),
(102, 1, 'Fadhil Djibran', '2025-04-23', 'Hadir'),
(103, 1, 'Fadhil Djibran', '2025-04-24', 'Izin'),
(104, 1, 'Fadhil Djibran', '2025-04-25', 'Hadir'),
(105, 1, 'Fadhil Djibran', '2025-04-28', 'Hadir'),
(106, 1, 'Fadhil Djibran', '2025-04-29', 'Sakit'),
(107, 1, 'Fadhil Djibran', '2025-04-30', 'Hadir'),
(108, 1, 'Fadhil Djibran', '2025-05-01', 'Hadir'),
(109, 1, 'Fadhil Djibran', '2025-05-02', 'Hadir'),
(110, 1, 'Fadhil Djibran', '2025-05-05', 'Hadir'),
(111, 1, 'Fadhil Djibran', '2025-05-06', 'Hadir'),
(112, 1, 'Fadhil Djibran', '2025-05-07', 'Hadir'),
(113, 1, 'Fadhil Djibran', '2025-05-08', 'Alpha'),
(114, 1, 'Fadhil Djibran', '2025-05-09', 'Hadir'),
(115, 1, 'Fadhil Djibran', '2025-05-12', 'Hadir'),
(116, 1, 'Fadhil Djibran', '2025-05-13', 'Izin'),
(117, 1, 'Fadhil Djibran', '2025-05-14', 'Hadir'),
(118, 1, 'Fadhil Djibran', '2025-05-15', 'Hadir'),
(119, 1, 'Fadhil Djibran', '2025-05-16', 'Hadir'),
(120, 1, 'Fadhil Djibran', '2025-05-19', 'Sakit'),
(121, 1, 'Fadhil Djibran', '2025-05-20', 'Hadir'),
(122, 1, 'Fadhil Djibran', '2025-05-21', 'Hadir'),
(123, 1, 'Fadhil Djibran', '2025-05-22', 'Hadir'),
(124, 1, 'Fadhil Djibran', '2025-05-23', 'Hadir'),
(125, 1, 'Fadhil Djibran', '2025-05-26', 'Hadir'),
(126, 2, 'Nasikh Andhyka', '2025-03-03', 'Hadir'),
(127, 2, 'Nasikh Andhyka', '2025-03-04', 'Hadir'),
(128, 2, 'Nasikh Andhyka', '2025-03-05', 'Hadir'),
(129, 2, 'Nasikh Andhyka', '2025-03-06', 'Izin'),
(130, 2, 'Nasikh Andhyka', '2025-03-07', 'Hadir'),
(131, 2, 'Nasikh Andhyka', '2025-03-10', 'Sakit'),
(132, 2, 'Nasikh Andhyka', '2025-03-11', 'Hadir'),
(133, 2, 'Nasikh Andhyka', '2025-03-12', 'Hadir'),
(134, 2, 'Nasikh Andhyka', '2025-03-13', 'Hadir'),
(135, 2, 'Nasikh Andhyka', '2025-03-14', 'Alpha'),
(136, 2, 'Nasikh Andhyka', '2025-03-17', 'Hadir'),
(137, 2, 'Nasikh Andhyka', '2025-03-18', 'Hadir'),
(138, 2, 'Nasikh Andhyka', '2025-03-19', 'Hadir'),
(139, 2, 'Nasikh Andhyka', '2025-03-20', 'Hadir'),
(140, 2, 'Nasikh Andhyka', '2025-03-21', 'Izin'),
(141, 2, 'Nasikh Andhyka', '2025-03-24', 'Hadir'),
(142, 2, 'Nasikh Andhyka', '2025-03-25', 'Hadir'),
(143, 2, 'Nasikh Andhyka', '2025-03-26', 'Sakit'),
(144, 2, 'Nasikh Andhyka', '2025-03-27', 'Hadir'),
(145, 2, 'Nasikh Andhyka', '2025-03-28', 'Hadir'),
(146, 2, 'Nasikh Andhyka', '2025-03-31', 'Hadir'),
(147, 2, 'Nasikh Andhyka', '2025-04-01', 'Hadir'),
(148, 2, 'Nasikh Andhyka', '2025-04-02', 'Hadir'),
(149, 2, 'Nasikh Andhyka', '2025-04-03', 'Alpha'),
(150, 2, 'Nasikh Andhyka', '2025-04-04', 'Hadir'),
(151, 2, 'Nasikh Andhyka', '2025-04-07', 'Hadir'),
(152, 2, 'Nasikh Andhyka', '2025-04-08', 'Izin'),
(153, 2, 'Nasikh Andhyka', '2025-04-09', 'Hadir'),
(154, 2, 'Nasikh Andhyka', '2025-04-10', 'Hadir'),
(155, 2, 'Nasikh Andhyka', '2025-04-11', 'Sakit'),
(156, 2, 'Nasikh Andhyka', '2025-04-14', 'Hadir'),
(157, 2, 'Nasikh Andhyka', '2025-04-15', 'Hadir'),
(158, 2, 'Nasikh Andhyka', '2025-04-16', 'Hadir'),
(159, 2, 'Nasikh Andhyka', '2025-04-17', 'Hadir'),
(160, 2, 'Nasikh Andhyka', '2025-04-18', 'Hadir'),
(161, 2, 'Nasikh Andhyka', '2025-04-21', 'Alpha'),
(162, 2, 'Nasikh Andhyka', '2025-04-22', 'Hadir'),
(163, 2, 'Nasikh Andhyka', '2025-04-23', 'Hadir'),
(164, 2, 'Nasikh Andhyka', '2025-04-24', 'Hadir'),
(165, 2, 'Nasikh Andhyka', '2025-04-25', 'Izin'),
(166, 2, 'Nasikh Andhyka', '2025-04-28', 'Hadir'),
(167, 2, 'Nasikh Andhyka', '2025-04-29', 'Hadir'),
(168, 2, 'Nasikh Andhyka', '2025-04-30', 'Sakit'),
(169, 2, 'Nasikh Andhyka', '2025-05-01', 'Hadir'),
(170, 2, 'Nasikh Andhyka', '2025-05-02', 'Hadir'),
(171, 2, 'Nasikh Andhyka', '2025-05-05', 'Hadir'),
(172, 2, 'Nasikh Andhyka', '2025-05-06', 'Hadir'),
(173, 2, 'Nasikh Andhyka', '2025-05-07', 'Alpha'),
(174, 2, 'Nasikh Andhyka', '2025-05-08', 'Hadir'),
(175, 2, 'Nasikh Andhyka', '2025-05-09', 'Hadir'),
(176, 2, 'Nasikh Andhyka', '2025-05-12', 'Hadir'),
(177, 2, 'Nasikh Andhyka', '2025-05-13', 'Hadir'),
(178, 2, 'Nasikh Andhyka', '2025-05-14', 'Izin'),
(179, 2, 'Nasikh Andhyka', '2025-05-15', 'Hadir'),
(180, 2, 'Nasikh Andhyka', '2025-05-16', 'Sakit'),
(181, 2, 'Nasikh Andhyka', '2025-05-19', 'Hadir'),
(182, 2, 'Nasikh Andhyka', '2025-05-20', 'Hadir'),
(183, 2, 'Nasikh Andhyka', '2025-05-21', 'Hadir'),
(184, 2, 'Nasikh Andhyka', '2025-05-22', 'Hadir'),
(185, 2, 'Nasikh Andhyka', '2025-05-23', 'Hadir'),
(186, 2, 'Nasikh Andhyka', '2025-05-26', 'Hadir'),
(187, 3, 'Syaifudin Afandi', '2025-03-03', 'Hadir'),
(188, 3, 'Syaifudin Afandi', '2025-03-04', 'Izin'),
(189, 3, 'Syaifudin Afandi', '2025-03-05', 'Hadir'),
(190, 3, 'Syaifudin Afandi', '2025-03-06', 'Hadir'),
(191, 3, 'Syaifudin Afandi', '2025-03-07', 'Sakit'),
(192, 3, 'Syaifudin Afandi', '2025-03-10', 'Hadir'),
(193, 3, 'Syaifudin Afandi', '2025-03-11', 'Hadir'),
(194, 3, 'Syaifudin Afandi', '2025-03-12', 'Alpha'),
(195, 3, 'Syaifudin Afandi', '2025-03-13', 'Hadir'),
(196, 3, 'Syaifudin Afandi', '2025-03-14', 'Hadir'),
(197, 3, 'Syaifudin Afandi', '2025-03-17', 'Hadir'),
(198, 3, 'Syaifudin Afandi', '2025-03-18', 'Hadir'),
(199, 3, 'Syaifudin Afandi', '2025-03-19', 'Izin'),
(200, 3, 'Syaifudin Afandi', '2025-03-20', 'Hadir'),
(201, 3, 'Syaifudin Afandi', '2025-03-21', 'Sakit'),
(202, 3, 'Syaifudin Afandi', '2025-03-24', 'Hadir'),
(203, 3, 'Syaifudin Afandi', '2025-03-25', 'Hadir'),
(204, 3, 'Syaifudin Afandi', '2025-03-26', 'Hadir'),
(205, 3, 'Syaifudin Afandi', '2025-03-27', 'Alpha'),
(206, 3, 'Syaifudin Afandi', '2025-03-28', 'Hadir'),
(207, 3, 'Syaifudin Afandi', '2025-03-31', 'Hadir'),
(208, 3, 'Syaifudin Afandi', '2025-04-01', 'Hadir'),
(209, 3, 'Syaifudin Afandi', '2025-04-02', 'Hadir'),
(210, 3, 'Syaifudin Afandi', '2025-04-03', 'Sakit'),
(211, 3, 'Syaifudin Afandi', '2025-04-04', 'Hadir'),
(212, 3, 'Syaifudin Afandi', '2025-04-07', 'Izin'),
(213, 3, 'Syaifudin Afandi', '2025-04-08', 'Hadir'),
(214, 3, 'Syaifudin Afandi', '2025-04-09', 'Hadir'),
(215, 3, 'Syaifudin Afandi', '2025-04-10', 'Hadir'),
(216, 3, 'Syaifudin Afandi', '2025-04-11', 'Alpha'),
(217, 3, 'Syaifudin Afandi', '2025-04-14', 'Hadir'),
(218, 3, 'Syaifudin Afandi', '2025-04-15', 'Hadir'),
(219, 3, 'Syaifudin Afandi', '2025-04-16', 'Hadir'),
(220, 3, 'Syaifudin Afandi', '2025-04-17', 'Sakit'),
(221, 3, 'Syaifudin Afandi', '2025-04-18', 'Hadir'),
(222, 3, 'Syaifudin Afandi', '2025-04-21', 'Hadir'),
(223, 3, 'Syaifudin Afandi', '2025-04-22', 'Izin'),
(224, 3, 'Syaifudin Afandi', '2025-04-23', 'Hadir'),
(225, 3, 'Syaifudin Afandi', '2025-04-24', 'Hadir'),
(226, 3, 'Syaifudin Afandi', '2025-04-25', 'Hadir'),
(227, 3, 'Syaifudin Afandi', '2025-04-28', 'Alpha'),
(228, 3, 'Syaifudin Afandi', '2025-04-29', 'Hadir'),
(229, 3, 'Syaifudin Afandi', '2025-04-30', 'Hadir'),
(230, 3, 'Syaifudin Afandi', '2025-05-01', 'Hadir'),
(231, 3, 'Syaifudin Afandi', '2025-05-02', 'Sakit'),
(232, 3, 'Syaifudin Afandi', '2025-05-05', 'Hadir'),
(233, 3, 'Syaifudin Afandi', '2025-05-06', 'Hadir'),
(234, 3, 'Syaifudin Afandi', '2025-05-07', 'Hadir'),
(235, 3, 'Syaifudin Afandi', '2025-05-08', 'Izin'),
(236, 3, 'Syaifudin Afandi', '2025-05-09', 'Hadir'),
(237, 3, 'Syaifudin Afandi', '2025-05-12', 'Hadir'),
(238, 3, 'Syaifudin Afandi', '2025-05-13', 'Alpha'),
(239, 3, 'Syaifudin Afandi', '2025-05-14', 'Hadir'),
(240, 3, 'Syaifudin Afandi', '2025-05-15', 'Hadir'),
(241, 3, 'Syaifudin Afandi', '2025-05-16', 'Hadir'),
(242, 3, 'Syaifudin Afandi', '2025-05-19', 'Hadir'),
(243, 3, 'Syaifudin Afandi', '2025-05-20', 'Sakit'),
(244, 3, 'Syaifudin Afandi', '2025-05-21', 'Hadir'),
(245, 3, 'Syaifudin Afandi', '2025-05-22', 'Hadir'),
(246, 3, 'Syaifudin Afandi', '2025-05-23', 'Izin'),
(247, 3, 'Syaifudin Afandi', '2025-05-26', 'Hadir'),
(248, 4, 'Rafi Walidain', '2025-03-03', 'Alpha'),
(249, 4, 'Rafi Walidain', '2025-03-04', 'Hadir'),
(250, 4, 'Rafi Walidain', '2025-03-05', 'Hadir'),
(251, 4, 'Rafi Walidain', '2025-03-06', 'Sakit'),
(252, 4, 'Rafi Walidain', '2025-03-07', 'Hadir'),
(253, 4, 'Rafi Walidain', '2025-03-10', 'Izin'),
(254, 4, 'Rafi Walidain', '2025-03-11', 'Hadir'),
(255, 4, 'Rafi Walidain', '2025-03-12', 'Hadir'),
(256, 4, 'Rafi Walidain', '2025-03-13', 'Hadir'),
(257, 4, 'Rafi Walidain', '2025-03-14', 'Hadir'),
(258, 4, 'Rafi Walidain', '2025-03-17', 'Alpha'),
(259, 4, 'Rafi Walidain', '2025-03-18', 'Hadir'),
(260, 4, 'Rafi Walidain', '2025-03-19', 'Hadir'),
(261, 4, 'Rafi Walidain', '2025-03-20', 'Sakit'),
(262, 4, 'Rafi Walidain', '2025-03-21', 'Hadir'),
(263, 4, 'Rafi Walidain', '2025-03-24', 'Izin'),
(264, 4, 'Rafi Walidain', '2025-03-25', 'Hadir'),
(265, 4, 'Rafi Walidain', '2025-03-26', 'Hadir'),
(266, 4, 'Rafi Walidain', '2025-03-27', 'Hadir'),
(267, 4, 'Rafi Walidain', '2025-03-28', 'Hadir'),
(268, 4, 'Rafi Walidain', '2025-03-31', 'Alpha'),
(269, 4, 'Rafi Walidain', '2025-04-01', 'Hadir'),
(270, 4, 'Rafi Walidain', '2025-04-02', 'Sakit'),
(271, 4, 'Rafi Walidain', '2025-04-03', 'Hadir'),
(272, 4, 'Rafi Walidain', '2025-04-04', 'Izin'),
(273, 4, 'Rafi Walidain', '2025-04-07', 'Hadir'),
(274, 4, 'Rafi Walidain', '2025-04-08', 'Hadir'),
(275, 4, 'Rafi Walidain', '2025-04-09', 'Hadir'),
(276, 4, 'Rafi Walidain', '2025-04-10', 'Hadir'),
(277, 4, 'Rafi Walidain', '2025-04-11', 'Alpha'),
(278, 4, 'Rafi Walidain', '2025-04-14', 'Hadir'),
(279, 4, 'Rafi Walidain', '2025-04-15', 'Sakit'),
(280, 4, 'Rafi Walidain', '2025-04-16', 'Hadir'),
(281, 4, 'Rafi Walidain', '2025-04-17', 'Izin'),
(282, 4, 'Rafi Walidain', '2025-04-18', 'Hadir'),
(283, 4, 'Rafi Walidain', '2025-04-21', 'Hadir'),
(284, 4, 'Rafi Walidain', '2025-04-22', 'Hadir'),
(285, 4, 'Rafi Walidain', '2025-04-23', 'Hadir'),
(286, 4, 'Rafi Walidain', '2025-04-24', 'Alpha'),
(287, 4, 'Rafi Walidain', '2025-04-25', 'Hadir'),
(288, 4, 'Rafi Walidain', '2025-04-28', 'Sakit'),
(289, 4, 'Rafi Walidain', '2025-04-29', 'Hadir'),
(290, 4, 'Rafi Walidain', '2025-04-30', 'Izin'),
(291, 4, 'Rafi Walidain', '2025-05-01', 'Hadir'),
(292, 4, 'Rafi Walidain', '2025-05-02', 'Hadir'),
(293, 4, 'Rafi Walidain', '2025-05-05', 'Hadir'),
(294, 4, 'Rafi Walidain', '2025-05-06', 'Alpha'),
(295, 4, 'Rafi Walidain', '2025-05-07', 'Hadir'),
(296, 4, 'Rafi Walidain', '2025-05-08', 'Sakit'),
(297, 4, 'Rafi Walidain', '2025-05-09', 'Hadir'),
(298, 4, 'Rafi Walidain', '2025-05-12', 'Izin'),
(299, 4, 'Rafi Walidain', '2025-05-13', 'Hadir'),
(300, 4, 'Rafi Walidain', '2025-05-14', 'Hadir'),
(301, 4, 'Rafi Walidain', '2025-05-15', 'Hadir'),
(302, 4, 'Rafi Walidain', '2025-05-16', 'Hadir'),
(303, 4, 'Rafi Walidain', '2025-05-19', 'Alpha'),
(304, 4, 'Rafi Walidain', '2025-05-20', 'Hadir'),
(305, 4, 'Rafi Walidain', '2025-05-21', 'Sakit'),
(306, 4, 'Rafi Walidain', '2025-05-22', 'Hadir'),
(307, 4, 'Rafi Walidain', '2025-05-23', 'Hadir'),
(308, 4, 'Rafi Walidain', '2025-05-26', 'Izin'),
(309, 5, 'Reno Maulidyan', '2025-03-03', 'Hadir'),
(310, 5, 'Reno Maulidyan', '2025-03-04', 'Hadir'),
(311, 5, 'Reno Maulidyan', '2025-03-05', 'Hadir'),
(312, 5, 'Reno Maulidyan', '2025-03-06', 'Hadir'),
(313, 5, 'Reno Maulidyan', '2025-03-07', 'Alpha'),
(314, 5, 'Reno Maulidyan', '2025-03-10', 'Hadir'),
(315, 5, 'Reno Maulidyan', '2025-03-11', 'Sakit'),
(316, 5, 'Reno Maulidyan', '2025-03-12', 'Hadir'),
(317, 5, 'Reno Maulidyan', '2025-03-13', 'Izin'),
(318, 5, 'Reno Maulidyan', '2025-03-14', 'Hadir'),
(319, 5, 'Reno Maulidyan', '2025-03-17', 'Hadir'),
(320, 5, 'Reno Maulidyan', '2025-03-18', 'Hadir'),
(321, 5, 'Reno Maulidyan', '2025-03-19', 'Alpha'),
(322, 5, 'Reno Maulidyan', '2025-03-20', 'Hadir'),
(323, 5, 'Reno Maulidyan', '2025-03-21', 'Hadir'),
(324, 5, 'Reno Maulidyan', '2025-03-24', 'Sakit'),
(325, 5, 'Reno Maulidyan', '2025-03-25', 'Hadir'),
(326, 5, 'Reno Maulidyan', '2025-03-26', 'Izin'),
(327, 5, 'Reno Maulidyan', '2025-03-27', 'Hadir'),
(328, 5, 'Reno Maulidyan', '2025-03-28', 'Hadir'),
(329, 5, 'Reno Maulidyan', '2025-03-31', 'Hadir'),
(330, 5, 'Reno Maulidyan', '2025-04-01', 'Hadir'),
(331, 5, 'Reno Maulidyan', '2025-04-02', 'Alpha'),
(332, 5, 'Reno Maulidyan', '2025-04-03', 'Hadir'),
(333, 5, 'Reno Maulidyan', '2025-04-04', 'Sakit'),
(334, 5, 'Reno Maulidyan', '2025-04-07', 'Hadir'),
(335, 5, 'Reno Maulidyan', '2025-04-08', 'Hadir'),
(336, 5, 'Reno Maulidyan', '2025-04-09', 'Izin'),
(337, 5, 'Reno Maulidyan', '2025-04-10', 'Hadir'),
(338, 5, 'Reno Maulidyan', '2025-04-11', 'Hadir'),
(339, 5, 'Reno Maulidyan', '2025-04-14', 'Hadir'),
(340, 5, 'Reno Maulidyan', '2025-04-15', 'Alpha'),
(341, 5, 'Reno Maulidyan', '2025-04-16', 'Hadir'),
(342, 5, 'Reno Maulidyan', '2025-04-17', 'Sakit'),
(343, 5, 'Reno Maulidyan', '2025-04-18', 'Hadir'),
(344, 5, 'Reno Maulidyan', '2025-04-21', 'Izin'),
(345, 5, 'Reno Maulidyan', '2025-04-22', 'Hadir'),
(346, 5, 'Reno Maulidyan', '2025-04-23', 'Hadir'),
(347, 5, 'Reno Maulidyan', '2025-04-24', 'Hadir'),
(348, 5, 'Reno Maulidyan', '2025-04-25', 'Hadir'),
(349, 5, 'Reno Maulidyan', '2025-04-28', 'Hadir'),
(350, 5, 'Reno Maulidyan', '2025-04-29', 'Alpha'),
(351, 5, 'Reno Maulidyan', '2025-04-30', 'Sakit'),
(352, 5, 'Reno Maulidyan', '2025-05-01', 'Hadir'),
(353, 5, 'Reno Maulidyan', '2025-05-02', 'Izin'),
(354, 5, 'Reno Maulidyan', '2025-05-05', 'Hadir'),
(355, 5, 'Reno Maulidyan', '2025-05-06', 'Hadir'),
(356, 5, 'Reno Maulidyan', '2025-05-07', 'Hadir'),
(357, 5, 'Reno Maulidyan', '2025-05-08', 'Hadir'),
(358, 5, 'Reno Maulidyan', '2025-05-09', 'Alpha'),
(359, 5, 'Reno Maulidyan', '2025-05-12', 'Hadir'),
(360, 5, 'Reno Maulidyan', '2025-05-13', 'Sakit'),
(361, 5, 'Reno Maulidyan', '2025-05-14', 'Hadir'),
(362, 5, 'Reno Maulidyan', '2025-05-15', 'Izin'),
(363, 5, 'Reno Maulidyan', '2025-05-16', 'Hadir'),
(364, 5, 'Reno Maulidyan', '2025-05-19', 'Hadir'),
(365, 5, 'Reno Maulidyan', '2025-05-20', 'Hadir'),
(366, 5, 'Reno Maulidyan', '2025-05-21', 'Hadir'),
(367, 5, 'Reno Maulidyan', '2025-05-22', 'Alpha'),
(368, 5, 'Reno Maulidyan', '2025-05-23', 'Sakit'),
(369, 5, 'Reno Maulidyan', '2025-05-26', 'Hadir');

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
(3, 3, 'Syaifudin Afandi', '2025-05-28', 'Izin', 'uploads/bukti_absensi/bukti_6836840522fb27.83165298.png', 'disetujui', '2025-05-28 03:33:25');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=375;

--
-- AUTO_INCREMENT for table `pengajuanabsensi`
--
ALTER TABLE `pengajuanabsensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

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

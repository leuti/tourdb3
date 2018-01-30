-- phpMyAdmin SQL Dump
-- version 4.4.15.5
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Jan 30, 2018 at 05:36 AM
-- Server version: 5.5.49-log
-- PHP Version: 7.0.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tourdb2_prod`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_grades`
--

CREATE TABLE IF NOT EXISTS `tbl_grades` (
  `grdCodeID` varchar(10) NOT NULL,
  `grdGroup` varchar(10) DEFAULT NULL,
  `grdTracksGroup` varchar(10) DEFAULT NULL,
  `grdType` varchar(50) DEFAULT NULL,
  `grdSort` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_grades`
--

INSERT INTO `tbl_grades` (`grdCodeID`, `grdGroup`, `grdTracksGroup`, `grdType`, `grdSort`) VALUES
('3a', '3', NULL, 'Klettergrad', 40),
('3b', '3', NULL, 'Klettergrad', 50),
('3c', '3', NULL, 'Klettergrad', 60),
('4a', '4', NULL, 'Klettergrad', 70),
('4b', '4', NULL, 'Klettergrad', 80),
('4c', '4', NULL, 'Klettergrad', 90),
('5a', '5', NULL, 'Klettergrad', 100),
('5b', '5', NULL, 'Klettergrad', 110),
('5c', '5', NULL, 'Klettergrad', 120),
('6a', '6', NULL, 'Klettergrad', 130),
('6b', '6', NULL, 'Klettergrad', 140),
('6c', '6', NULL, 'Klettergrad', 150),
('??', '??', '??', 'Schwierigkeitsgrad', 999),
('AS', 'AS', 'AS', 'Schwierigkeitsgrad', 350),
('E1', NULL, NULL, 'Ernsthaftigkeit', 10),
('E2', NULL, NULL, 'Ernsthaftigkeit', 20),
('E3', NULL, NULL, 'Ernsthaftigkeit', 30),
('E4', NULL, NULL, 'Ernsthaftigkeit', 40),
('E5', NULL, NULL, 'Ernsthaftigkeit', 50),
('EX', 'EX', 'EX', 'Schwierigkeitsgrad', 380),
('I', '1', NULL, 'Klettergrad', 350),
('II', '2', NULL, 'Klettergrad', 360),
('II+', '2', NULL, 'Klettergrad', 370),
('III', '3', NULL, 'Klettergrad', 410),
('III+', '3', NULL, 'Klettergrad', 420),
('III-', '3', NULL, 'Klettergrad', 400),
('IV', '4', NULL, 'Klettergrad', 460),
('IV+', '4', NULL, 'Klettergrad', 470),
('IV-', '4', NULL, 'Klettergrad', 450),
('L', 'LE', 'LE', 'Schwierigkeitsgrad', 110),
('L+', 'LE', 'LE', 'Schwierigkeitsgrad', 120),
('S', 'SC', 'SC', 'Schwierigkeitsgrad', 260),
('S+', 'SC', 'SC', 'Schwierigkeitsgrad', 270),
('S-', 'SC', 'SC', 'Schwierigkeitsgrad', 250),
('SS', 'SS', 'SS', 'Schwierigkeitsgrad', 310),
('SS+', 'SS', 'SS', 'Schwierigkeitsgrad', 320),
('SS-', 'SS', 'SS', 'Schwierigkeitsgrad', 300),
('T1', 'T4', 'T4', 'Schwierigkeitsgrad', 10),
('T2', 'T4', 'T4', 'Schwierigkeitsgrad', 20),
('T3', 'T4', 'T4', 'Schwierigkeitsgrad', 30),
('T3+', 'T4', 'T4', 'Schwierigkeitsgrad', 32),
('T3-', 'T4', 'T4', 'Schwierigkeitsgrad', 28),
('T4', 'T4', 'T4', 'Schwierigkeitsgrad', 40),
('T4+', 'T4', 'T4', 'Schwierigkeitsgrad', 42),
('T4-', 'T4', 'T4', 'Schwierigkeitsgrad', 38),
('T5', 'T6', 'T6', 'Schwierigkeitsgrad', 50),
('T5+', 'T6', 'T6', 'Schwierigkeitsgrad', 52),
('T5-', 'T6', 'T6', 'Schwierigkeitsgrad', 48),
('T6', 'T6', 'T6', 'Schwierigkeitsgrad', 60),
('T6+', 'T6', 'T6', 'Schwierigkeitsgrad', 62),
('V', '5', NULL, 'Klettergrat', 490),
('V+', '5', NULL, 'Klettergrat', 500),
('V-', '4', NULL, 'Klettergrad', 480),
('WS', 'WS', 'WS', 'Schwierigkeitsgrad', 160),
('WS+', 'WS', 'WS+', 'Schwierigkeitsgrad', 170),
('WS-', 'WS', 'WS-', 'Schwierigkeitsgrad', 150),
('ZS', 'ZS', 'ZS', 'Schwierigkeitsgrad', 210),
('ZS+', 'ZS', 'ZS+', 'Schwierigkeitsgrad', 220),
('ZS-', 'ZS', 'ZS-', 'Schwierigkeitsgrad', 200);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_grades`
--
ALTER TABLE `tbl_grades`
  ADD PRIMARY KEY (`grdCodeID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.4.15.5
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Jan 30, 2018 at 05:37 AM
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
-- Table structure for table `tbl_sources`
--

CREATE TABLE IF NOT EXISTS `tbl_sources` (
  `srcCode` varchar(50) NOT NULL,
  `srcName` varchar(255) DEFAULT NULL,
  `srcRemarks` varchar(1024) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_sources`
--

INSERT INTO `tbl_sources` (`srcCode`, `srcName`, `srcRemarks`) VALUES
('4.', 'Viertausender der Alpen', NULL),
('be', 'Hochtouren Topoführer - 70 klassische Hochtouren zwischen Les Diablerets und Grimsel', NULL),
('hl', 'Himmelsleitern', NULL),
('mdw', 'SAC Alpine Touren - Walliser Alpen - Matterhorn - Dent Blanche - Weisshorn', NULL),
('oa', 'Ostalpen - Rother Selection', NULL),
('own', 'Eigene Touren', NULL),
('pa', 'Plaisir Alpin', NULL),
('ss-ch', 'Die schönsten Skitouren der Schweiz', '1. Auflage / 2003'),
('vs', 'Hochtouren Topoführer - 77 Touren zwischen Mont Dolent und Fletschhorn', NULL),
('wa', 'Westalpen - Rother Selection', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_sources`
--
ALTER TABLE `tbl_sources`
  ADD PRIMARY KEY (`srcCode`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

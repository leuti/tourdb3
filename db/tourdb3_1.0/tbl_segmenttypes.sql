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
-- Table structure for table `tbl_segmenttypes`
--

CREATE TABLE IF NOT EXISTS `tbl_segmenttypes` (
  `stypCode` varchar(50) NOT NULL,
  `stypName` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_segmenttypes`
--

INSERT INTO `tbl_segmenttypes` (`stypCode`, `stypName`) VALUES
('AW', 'Alpinwandern'),
('HT', 'Hochtour'),
('MK', 'Mehrseilkletterroute'),
('SS', 'Schneeschuhtour'),
('ST', 'Skitour'),
('WA', 'Wandern'),
('WW', 'Winterwanderung');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_segmenttypes`
--
ALTER TABLE `tbl_segmenttypes`
  ADD PRIMARY KEY (`stypCode`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

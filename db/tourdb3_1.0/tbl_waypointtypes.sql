-- phpMyAdmin SQL Dump
-- version 4.4.15.5
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Jan 30, 2018 at 05:38 AM
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
-- Table structure for table `tbl_waypointtypes`
--

CREATE TABLE IF NOT EXISTS `tbl_waypointtypes` (
  `wtypID` int(11) NOT NULL,
  `wtypCode` varchar(50) DEFAULT NULL,
  `wtypNameShort` varchar(50) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_waypointtypes`
--

INSERT INTO `tbl_waypointtypes` (`wtypID`, `wtypCode`, `wtypNameShort`) VALUES
(1, 'bst', 'Bergstation'),
(2, 'tal', 'Talort'),
(3, 'wpt', 'Wegpunkt'),
(4, 'hu', 'Hütte'),
(5, 'gi', 'Gipfel');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_waypointtypes`
--
ALTER TABLE `tbl_waypointtypes`
  ADD PRIMARY KEY (`wtypID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_waypointtypes`
--
ALTER TABLE `tbl_waypointtypes`
  MODIFY `wtypID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

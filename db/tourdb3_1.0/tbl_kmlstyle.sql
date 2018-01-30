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
-- Table structure for table `tbl_kmlstyle`
--

CREATE TABLE IF NOT EXISTS `tbl_kmlstyle` (
  `ID` int(11) NOT NULL,
  `styCode` varchar(8) NOT NULL,
  `styColorNormal` varchar(8) NOT NULL,
  `styWidthNormal` int(11) NOT NULL,
  `styLineNormal` varchar(30) DEFAULT NULL,
  `styColorHighlighted` varchar(8) DEFAULT NULL,
  `styWidthHighlighted` int(11) DEFAULT NULL,
  `styLineHighlighted` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_kmlstyle`
--

INSERT INTO `tbl_kmlstyle` (`ID`, `styCode`, `styColorNormal`, `styWidthNormal`, `styLineNormal`, `styColorHighlighted`, `styWidthHighlighted`, `styLineHighlighted`) VALUES
(0, 'AWT3', 'ff00ffff', 3, 'ausgezogen', 'ff00ffff', 3, 'ausgezogen'),
(1, 'AWT4', 'ff0055aa', 3, 'ausgezogen', 'ff0055aa', 3, 'ausgezogen'),
(2, 'AWT6', 'ff003faa', 3, 'ausgezogen', 'ff003faa', 3, 'ausgezogen'),
(3, 'HTEX', 'ff000000', 3, 'gepunktet', 'ff000000', 3, 'gepunktet'),
(4, 'HTLE', 'ff00aa00', 3, 'gepunktet', 'ff7fffaa', 3, 'gepunktet'),
(5, 'HTSC', 'ff0000ff', 3, 'gepunktet', 'ff0000ff', 3, 'gepunktet'),
(6, 'HTSS', 'ff000000', 3, 'gepunktet', 'ff000000', 3, 'gepunktet'),
(7, 'HTWS', 'ff7cffe7', 3, 'gepunktet', 'ff00ffff', 3, 'gepunktet'),
(8, 'HTWS-', 'ff7fffaa', 3, 'ausgezogen', 'ff7cffe7', 3, 'ausgezogen'),
(9, 'HTWS+', 'ff07e2ff', 3, 'ausgezogen', 'ff07e2ff', 3, 'ausgezogen'),
(10, 'HTXX', 'ffcdccd8', 1, 'ausgezogen', 'ffcdccd8', 1, 'ausgezogen'),
(11, 'HTZS', 'ff0055ff', 3, 'gepunktet', 'ff0055ff', 3, 'gepunktet'),
(12, 'HTZS-', 'ff049df5', 3, 'ausgezogen', 'ff049df5', 3, 'ausgezogen'),
(13, 'HTZS+', 'ff004ce6', 3, 'ausgezogen', 'ff004ce6', 3, 'ausgezogen'),
(14, 'oths', 'ffcdccd8', 3, 'nochwasanderes', 'ff00ffff', 3, 'nochwasanderes'),
(15, 'SSEX', 'ff00ffff', 3, 'gestrichelt', 'ff00ffff', 3, 'gestrichelt'),
(16, 'SSLE', 'ff00ffff', 3, 'gestrichelt', 'ff00ffff', 3, 'gestrichelt'),
(17, 'SSSC', 'ff00ffff', 3, 'gestrichelt', 'ff00ffff', 3, 'gestrichelt'),
(18, 'SSSS', 'ff00ffff', 3, 'gestrichelt', 'ff00ffff', 3, 'gestrichelt'),
(19, 'SST4', 'ff00ffff', 3, 'gestrichelt', 'ff00ffff', 3, 'gestrichelt'),
(20, 'SST6', 'ff00ffff', 3, 'gestrichelt', 'ff00ffff', 3, 'gestrichelt'),
(21, 'SSWS', 'ff00ffff', 3, 'gestrichelt', 'ff00ffff', 3, 'gestrichelt'),
(22, 'SSXX', 'ffcdccd8', 1, 'ausgezogen', 'ffcdccd8', 1, 'ausgezogen'),
(23, 'SSZS', 'ff00ffff', 3, 'gestrichelt', 'ff00ffff', 3, 'gestrichelt'),
(24, 'STEX', 'ff000000', 7, 'längergestrichelt', 'ff00ffff', 7, 'längergestrichelt'),
(25, 'STLE', 'ff00aa00', 7, 'längergestrichelt', 'ff00ffff', 7, 'längergestrichelt'),
(26, 'STSC', 'ff0000ff', 7, 'längergestrichelt', 'ff00ffff', 7, 'längergestrichelt'),
(27, 'STSS', 'ff000000', 7, 'längergestrichelt', 'ff00ffff', 7, 'längergestrichelt'),
(30, 'STWS', 'ff7cffe7', 7, 'längergestrichelt', 'ff00ffff', 7, 'längergestrichelt'),
(31, 'STXX', 'ffcdccd8', 7, 'ausgezogen', 'ffcdccd8', 7, 'ausgezogen'),
(32, 'STZS', 'ff0055ff', 7, 'längergestrichelt', 'ff00ffff', 7, 'längergestrichelt'),
(33, 'WAT4', 'ff00aaaa', 3, 'ausgezogen', 'ff00aaaa', 3, 'ausgezogen'),
(34, 'WAWS', 'ff00ffff', 3, 'ausgezogen', 'ff00ffff', 3, 'ausgezogen'),
(35, 'WAZS', 'ff00ffff', 3, 'ausgezogen', 'ff00ffff', 3, 'ausgezogen'),
(36, 'AWT2', 'ff00ffff', 3, 'ausgezogen', 'ff00ffff', 3, 'ausgezogen'),
(37, 'AWT1', 'ff00ffff', 3, 'ausgezogen', 'ff00ffff', 3, 'ausgezogen'),
(38, 'STWS+', 'ff07e2ff', 7, 'längergestrichelt', 'ff07e2ff', 7, 'längergestrichelt'),
(39, 'STWS-', 'ff7fffaa', 7, 'längergestrichelt', 'ff7fffaa', 7, 'längergestrichelt'),
(40, 'STZS-', 'ff049df5', 7, 'längergestrichelt', 'ff049df5', 7, 'längergestrichelt'),
(41, 'STZS+', 'ff004ce6', 7, 'längergestrichelt', 'ff004ce6', 7, 'längergestrichelt');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_kmlstyle`
--
ALTER TABLE `tbl_kmlstyle`
  ADD UNIQUE KEY `ID` (`ID`),
  ADD UNIQUE KEY `styCode` (`styCode`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

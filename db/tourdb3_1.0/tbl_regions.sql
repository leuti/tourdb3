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
-- Table structure for table `tbl_regions`
--

CREATE TABLE IF NOT EXISTS `tbl_regions` (
  `regID` int(11) NOT NULL,
  `regNameShort` varchar(50) DEFAULT NULL,
  `regNameLong` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_regions`
--

INSERT INTO `tbl_regions` (`regID`, `regNameShort`, `regNameLong`) VALUES
(1, 'VS3', 'VS3: Matterhorn, Dent Blanche, Weisshorn'),
(2, 'unk', 'unknown'),
(3, 'BE1', 'BE1: Wildhorn/Wildstrubel Blüemlisalp'),
(4, 'BE3', 'BE3: Bietschhorn / Nesthorn / Aletschhorn'),
(5, 'BE4', 'BE4: Jungfrau Region'),
(6, 'BE5', 'BE5: Von Grindelwald zur Grimsel'),
(7, 'BV1', 'BV1: Berner Voralpen'),
(8, 'CF1', 'CF1: Guide Chaîne franco-suisse'),
(9, 'GL1', 'GL1: Glarner Alpen'),
(10, 'GR1', 'GR1: Ringelspitz/Arosa/Rätikon'),
(11, 'GR10', 'GR10: Mittleres Engadin und Puschlav'),
(12, 'GR2', 'GR2: Vom Lukmanier zum Domleschg'),
(13, 'GR3', 'GR3: Avers (San Bernardino bis Septimer)'),
(14, 'GR4', 'GR4: Südliches Bergell - Disgrazia'),
(15, 'GR5', 'GR5: Bernina-Gruppe und Valposchiavo'),
(16, 'GR6', 'GR6: Vom Septimer zum Flüela'),
(17, 'GR7', 'GR7: Silvretta/Unterengadin/Münstertal'),
(18, 'SG1', 'SG1: Säntis-Churfirsten'),
(19, 'TI1', 'TI1: Vom Gridone zum Sankt Gotthard'),
(20, 'TI2', 'TI2: Von der Cristallina zum Sassariente'),
(21, 'TI3', 'TI3: Von der Piora zum Pizzo di Claro'),
(22, 'TI4', 'TI4:Vom Zapporthorn zum Passo San Jorio'),
(23, 'TI5', 'TI5: Vom Passo San Jorio zum Generoso'),
(24, 'UR1', 'UR1: Gotthard'),
(25, 'UR2', 'UR2: Göscheneralp - Furka - Grimsel'),
(26, 'UR3', 'UR3: Vom Susten zum Uri-Rotstock'),
(27, 'UR4', 'UR4: Oberalpstock, Windgällen'),
(28, 'VD1', 'VD1: Alpes et Préalpes vaudoises'),
(29, 'VS1/2', 'VS1/2: Mont Dolent, Grand Combin, Pigne d''Arolla'),
(30, 'VS4/5', 'VS4/5: Vom Theodulpass zum Simplon'),
(31, 'VS6', 'VS6: Simplon / Binntal / Nufenen'),
(32, 'ZS1', 'ZS1: Zentralschweizer Alpen'),
(33, 'ZSVA1', 'ZSVA1: Zentralschweizer Voralpen'),
(40, 'FR1', 'FR1: Préalpes Fribourgeoises');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_regions`
--
ALTER TABLE `tbl_regions`
  ADD PRIMARY KEY (`regID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_regions`
--
ALTER TABLE `tbl_regions`
  MODIFY `regID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=41;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

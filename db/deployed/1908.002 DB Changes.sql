ALTER TABLE `tbl_types` CHANGE `typPurpose` `typPurpose` VARCHAR(10) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL COMMENT 'Tracks, Waypoints, Segments';
UPDATE tbl_types SET typType = 'subtype' WHERE tbl_types.typType = 'stype';


-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 15. Aug 2019 um 19:43
-- Server-Version: 5.7.24-log
-- PHP-Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `tourdb2_prod`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_grades`
--

CREATE TABLE `tbl_grades` (
  `grdCodeID` varchar(10) NOT NULL,
  `grdGroup` varchar(10) DEFAULT NULL,
  `grdTracksGroup` varchar(10) DEFAULT NULL,
  `grdType` varchar(50) DEFAULT NULL,
  `grdSort` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `tbl_grades`
--

INSERT INTO `tbl_grades` (`grdCodeID`, `grdGroup`, `grdTracksGroup`, `grdType`, `grdSort`) VALUES
('3a', '3', NULL, 'Klettergrad', 34),
('3b', '3', NULL, 'Klettergrad', 36),
('3c', '3', NULL, 'Klettergrad', 38),
('4a', '4', NULL, 'Klettergrad', 40),
('4b', '4', NULL, 'Klettergrad', 42),
('4c', '4', NULL, 'Klettergrad', 44),
('5a', '5', NULL, 'Klettergrad', 50),
('5b', '5', NULL, 'Klettergrad', 52),
('5c', '5', NULL, 'Klettergrad', 54),
('6a', '6', NULL, 'Klettergrad', 60),
('6b', '6', NULL, 'Klettergrad', 62),
('6c', '6', NULL, 'Klettergrad', 64),
('??', '??', '??', 'Schwierigkeitsgrad', 999),
('AS', 'AS', 'AS', 'Schwierigkeitsgrad', 350),
('E1', 'E1', 'E1', 'Ernsthaftigkeit', 10),
('E2', 'E2', 'E2', 'Ernsthaftigkeit', 20),
('E3', 'E3', 'E3', 'Ernsthaftigkeit', 30),
('E4', 'E4', 'E4', 'Ernsthaftigkeit', 40),
('E5', 'E5', 'E5', 'Ernsthaftigkeit', 50),
('EX', 'EX', 'EX', 'Schwierigkeitsgrad', 380),
('I', '1', NULL, 'Klettergrad', 10),
('II', '2', NULL, 'Klettergrad', 20),
('II+', '2', NULL, 'Klettergrad', 22),
('III', '3', NULL, 'Klettergrad', 32),
('III+', '3', NULL, 'Klettergrad', 33),
('III-', '3', NULL, 'Klettergrad', 30),
('IV', '4', NULL, 'Klettergrad', 47),
('IV+', '4', NULL, 'Klettergrad', 48),
('IV-', '4', NULL, 'Klettergrad', 46),
('L', 'LE', 'LE', 'Schwierigkeitsgrad', 110),
('L+', 'LE', 'LE', 'Schwierigkeitsgrad', 120),
('S', 'SC', 'SC', 'Schwierigkeitsgrad', 260),
('S+', 'SC', 'SC', 'Schwierigkeitsgrad', 270),
('S-', 'SC', 'SC', 'Schwierigkeitsgrad', 250),
('SS', 'SS', 'SS', 'Schwierigkeitsgrad', 310),
('SS+', 'SS', 'SS', 'Schwierigkeitsgrad', 320),
('SS-', 'SS', 'SS', 'Schwierigkeitsgrad', 300),
('T1', 'T1', 'T1', 'Schwierigkeitsgrad', 10),
('T2', 'T2', 'T2', 'Schwierigkeitsgrad', 20),
('T3', 'T3', 'T3', 'Schwierigkeitsgrad', 30),
('T3+', 'T3', 'T3', 'Schwierigkeitsgrad', 32),
('T3-', 'T3', 'T3', 'Schwierigkeitsgrad', 28),
('T4', 'T4', 'T4', 'Schwierigkeitsgrad', 40),
('T4+', 'T4', 'T4', 'Schwierigkeitsgrad', 42),
('T4-', 'T4', 'T4', 'Schwierigkeitsgrad', 38),
('T5', 'T5', 'T5', 'Schwierigkeitsgrad', 50),
('T5+', 'T5', 'T5', 'Schwierigkeitsgrad', 52),
('T5-', 'T5', 'T5', 'Schwierigkeitsgrad', 48),
('T6', 'T6', 'T6', 'Schwierigkeitsgrad', 60),
('T6+', 'T6', 'T6', 'Schwierigkeitsgrad', 62),
('T6-', 'T6', 'T6', 'Schwierigkeitsgrad', 58),
('V', '5', NULL, 'Klettergrat', 56),
('V+', '5', NULL, 'Klettergrat', 58),
('V-', '4', NULL, 'Klettergrad', 49),
('WS', 'WS', 'WS', 'Schwierigkeitsgrad', 160),
('WS+', 'WS', 'WS+', 'Schwierigkeitsgrad', 170),
('WS-', 'WS', 'WS-', 'Schwierigkeitsgrad', 150),
('ZS', 'ZS', 'ZS', 'Schwierigkeitsgrad', 210),
('ZS+', 'ZS', 'ZS+', 'Schwierigkeitsgrad', 220),
('ZS-', 'ZS', 'ZS-', 'Schwierigkeitsgrad', 200);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `tbl_grades`
--
ALTER TABLE `tbl_grades`
  ADD PRIMARY KEY (`grdCodeID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

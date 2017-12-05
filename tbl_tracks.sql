-- phpMyAdmin SQL Dump
-- version 4.4.15.5
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Dec 01, 2017 at 09:15 PM
-- Server version: 5.5.49-log
-- PHP Version: 5.6.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tourdb3`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_segments`
--
CREATE TABLE IF NOT EXISTS `tbl_tracks` (
  `trkId` int(11) NOT NULL COMMENT 'Track ID', 
  `trkLogbookId` int(11) DEFAULT NULL COMMENT 'Logbook ID',
  `trkStravaFileName` varchar(100) DEFAULT NULL COMMENT 'File name Strava',
  `trkPeakRef` int(11) DEFAULT NULL COMMENT 'RefGipfel im Logbook',
  `trkDateBegin` date DEFAULT NULL COMMENT 'Date when the track started', 
  `trkDateFinish` date DEFAULT NULL COMMENT 'Date when the track finished (will be set to trkDateBegin when empty)', 
  `trkGPSStartTime` date DEFAULT NULL COMMENT 'Content of GPX gpx->metadata->time>',
  `trkSaison` varchar(50) DEFAULT NULL COMMENT 'Saison free text --> change to Foreign Key',   -- CHECK Int(11)
  `trkTyp` varchar(50) DEFAULT NULL COMMENT 'Type free text --> change to Foreign Key',   -- CHECK Int(11)
  `trkSubType` varchar(50) DEFAULT NULL COMMENT 'Subtype free text --> change to Foreign Key',   -- CHECK Int(11)
  `trkOrg` varchar(50) DEFAULT NULL COMMENT 'Type of organisation',
  `trkTarget` varchar(100) DEFAULT NULL COMMENT 'Target of the track --> change to Foreign Key',
  `trkRoute` varchar(255) DEFAULT NULL COMMENT 'Key waypoints on the route',
  `trkOvernightLoc` varchar(50) DEFAULT NULL COMMENT 'Name of hut/hotel --> change to Foreign Key',   -- CHECK Int(11)
  `trkParticipants` varchar(255) DEFAULT NULL COMMENT 'Name of Participants',
  `trkEvent` varchar(50) DEFAULT NULL COMMENT 'Type of event',
  `trkRemarks` varchar(1024) DEFAULT NULL COMMENT 'Remarks',
  `trkDistance` numeric(4,3) DEFAULT NULL COMMENT 'Distance in km',
  `trkTimeToTarget` time DEFAULT NULL COMMENT 'Time from start location to target/end',   -- CHECK type
  `trkTimeToEnd` time DEFAULT NULL COMMENT 'Time from target to end',   -- CHECK type
  `trkGrade` int(11) DEFAULT NULL COMMENT 'Schwierigkeitsgrad',
  `trkMeterUp` tinyint DEFAULT NULL COMMENT 'Meters ascended',   -- CHECK 99999
  `trkMeterDown` tinyint DEFAULT NULL COMMENT 'Meters descended'   -- CHECK 99999
  ) ENGINE=InnoDB AUTO_INCREMENT=1415 DEFAULT CHARSET=utf8mb4;    -- CHECK 

--
-- Indexes for table `tbl_segments`
--
ALTER TABLE `tbl_tracks`
  ADD PRIMARY KEY (`trkId`) USING BTREE;

--
-- AUTO_INCREMENT for table `tbl_segments`
--
ALTER TABLE `tbl_tracks`
  MODIFY `trkId` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;


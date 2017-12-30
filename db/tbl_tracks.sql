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

-- COLLATE utf8mb4_general_ci
-- COLLATE utf8_general_ci
--
-- Table structure for table `tbl_tracks`
--
CREATE TABLE IF NOT EXISTS `tbl_tracks` (
  `trkId` int(11) NOT NULL COMMENT 'Track ID', 
  `trkLogbookId` int(11) DEFAULT NULL COMMENT 'Logbook ID',
  `trkSourceFileName` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'File name Strava',
  `trkPeakRef` int(11) DEFAULT NULL COMMENT 'RefGipfel im Logbook',
  `trkTrackName` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Target of the track',
  `trkRoute` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Key waypoints on the route',
  `trkDateBegin` date DEFAULT NULL COMMENT 'Date when the track started', 
  `trkDateFinish` date DEFAULT NULL COMMENT 'Date when the track finished (will be set to trkDateBegin when empty)', 
  `trkGPSStartTime` DATETIME NULL DEFAULT NULL COMMENT 'Content of GPX gpx->metadata->time>',
  `trkSaison` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Saison free text', 
  `trkType` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Type free text',   
  `trkSubType` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Subtype free text',
  `trkOrg` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Type of organisation',
  `trkOvernightLoc` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Name of hut/hotel',
  `trkParticipants` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Name of Participants',
  `trkEvent` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Type of event',
  `trkRemarks` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Remarks',
  `trkDistance` int(5) DEFAULT NULL COMMENT 'Distance in km',
  `trkTimeOverall` time DEFAULT NULL COMMENT 'Overall time for track',
  `trkTimeToTarget` time DEFAULT NULL COMMENT 'Time from start location to target/end', 
  `trkTimeToEnd` time DEFAULT NULL COMMENT 'Time from target to end',  
  `trkGrade` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Schwierigkeitsgrad',
  `trkMeterUp` int(5) DEFAULT NULL COMMENT 'Meters ascended',
  `trkMeterDown` int(5) DEFAULT NULL COMMENT 'Meters descended',
  `trkCountry` varchar(2) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Country',
  `trkToReview` int(1) NOT NULL DEFAULT '0' COMMENT 'Record needs to be reviewed'
  ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

--
-- Indexes for table `tbl_tracks`
--
ALTER TABLE `tbl_tracks`
  ADD PRIMARY KEY (`trkId`) USING BTREE;

--
-- AUTO_INCREMENT for table `tbl_tracks`
--
ALTER TABLE `tbl_tracks`
  MODIFY `trkId` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;


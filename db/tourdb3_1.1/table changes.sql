-- =================================================================================
-- These are the table changes which need to be applied on top of tourdb3_1.0 files
-- =================================================================================


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


-- ==============================================================================
-- Table tbl_track_wayp
-- ==============================================================================

--
-- Table structure for table `tbl_track_wayp`
--

CREATE TABLE IF NOT EXISTS `tbl_track_wayp` (
  `trwpId` int(11) NOT NULL COMMENT 'record ID',
  `trwpTrkId` int(11) NOT NULL COMMENT 'ID of related track',
  `trwpWaypID` int(11) NOT NULL COMMENT 'ID of waypoint',
  `trwpWaypType` int(11) NOT NULL COMMENT 'ID of type f waypoint (e.g. peak/waypoint)',
  `trwpReached_f` tinyint(1) NOT NULL COMMENT 'True if reached'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Table links tracks with waypoints';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_track_wayp`
--
ALTER TABLE `tbl_track_wayp`
  ADD PRIMARY KEY (`trwpId`) USING BTREE,
  ADD KEY `trwp_trkid_waypid` (`trwpTrkId`,`trwpWaypID`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_track_wayp`
--
ALTER TABLE `tbl_track_wayp`
  MODIFY `trwpId` int(11) NOT NULL AUTO_INCREMENT COMMENT 'record ID';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



-- ==============================================================================
-- Table tbl_part
-- ==============================================================================

--
-- Table structure for table `tbl_part`
--

CREATE TABLE IF NOT EXISTS `tbl_part` (
  `prtId` int(11) NOT NULL,
  `prtFirstName` varchar(30) CHARACTER SET utf8 NOT NULL COMMENT 'Firstname',
  `prtLastName` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Lastname',
  `prtShort` varchar(10) CHARACTER SET utf8 NOT NULL COMMENT 'Shortname'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='Table of participants';

--
-- Dumping data for table `tbl_part`
--

INSERT INTO `tbl_part` (`prtId`, `prtFirstName`, `prtLastName`, `prtShort`) VALUES
(1, 'Bettina', 'Leutwyler', 'bele'),
(2, 'Urs', 'Gisler', 'urgi'),
(3, 'Gerry', 'Büeler', 'gebu'),
(4, 'Alex', 'Müdespacher', 'almu')
;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_part`
--
ALTER TABLE `tbl_part`
  ADD PRIMARY KEY (`prtId`) USING BTREE,
  ADD UNIQUE KEY `prtShort` (`prtShort`),
  ADD UNIQUE KEY `prtFirstName` (`prtFirstName`,`prtLastName`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_part`
--
ALTER TABLE `tbl_part`
  MODIFY `prtId` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

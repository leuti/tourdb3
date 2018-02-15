CREATE TABLE `tbl_track_wayp` 
(
    `trwpId` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'record ID',
    `trwpTrkId` INT(11) NOT NULL COMMENT 'ID of related track',
    `trwpWaypID` INT(11) NOT NULL COMMENT 'ID of waypoint',
    `trwpWaypType` INT(11) NOT NULL COMMENT 'ID of type f waypoint (e.g. peak/waypoint)',
    `trwpReached_f` TINYINT(1) NOT NULL COMMENT 'True if reached',
    PRIMARY KEY `trwpId` (`trwpId`) USING BTREE, 
    INDEX `trwp_trkid_waypid` (`trwpTrkId`, `trwpWaypID`) USING BTREE 
) 
ENGINE = InnoDB 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci 
COMMENT = 'Table links tracks with waypoints';


CREATE TABLE `tbl_part` 
( 
    `prtId` INT(11) NOT NULL AUTO_INCREMENT , 
    `prtFirstName` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Firstname', 
    `prtLastName` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Lastname' , 
    `prtShort` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Shortname' , 
    PRIMARY KEY `prtId` (`prtId`) USING BTREE
) 
ENGINE = InnoDB
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci 
COMMENT = 'Table of participants';
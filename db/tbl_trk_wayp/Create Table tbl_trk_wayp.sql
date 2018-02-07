CREATE TABLE `tourdb2_dev`.`tbl_track_wayp` 
    ( `trwpId` INT(11) NOT NULL AUTO_INCREMENT 
    , `trwpTrkId` INT(11) NOT NULL 
    , `trwpWaypID` INT(11) NOT NULL
    ,  `trwpWaypType` INT(11) NOT NULL
    , PRIMARY KEY (`trwpId`) USING BTREE
    , INDEX `trwp_trkid_waypid` (`trwpTrkId`, `trwpWaypID`) USING BTREE 
    ) 
    ENGINE = InnoDB 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_general_ci 
    COMMENT = 'Table links tracks with waypoints';
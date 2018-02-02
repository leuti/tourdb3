CREATE TABLE `tourdb2_dev`.`tbl_track_wayp` 
    ( `trwpId` INT(11) NOT NULL AUTO_INCREMENT 
    , `trkId` INT(11) NOT NULL 
    , `waypID` INT(11) NOT NULL 
    , PRIMARY KEY (`trwpId`) USING BTREE
    , INDEX `trwp_trkid_waypid` (`trkId`, `waypID`) USING BTREE 
    ) 
    ENGINE = InnoDB 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_general_ci 
    COMMENT = 'Table links tracks with waypoints';
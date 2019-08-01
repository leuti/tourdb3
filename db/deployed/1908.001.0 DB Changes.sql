-- Create Types / SubTypes Table
CREATE TABLE `tourdb2_prod`.`tbl_types` ( 
    `typId` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique Primary Id' , 
    `typCode` VARCHAR(10) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL COMMENT 'Code of Type (e.g. SST)' , 
    `typName` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , 
    `typParentId` INT(11) NULL COMMENT 'Points to partent type if subtype' , 
    `typType` VARCHAR(10) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL COMMENT 'Type or Subtype' , 
    `typPurpose` VARCHAR(5) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL COMMENT 'Tracks, Waypoints, Segments' , 
    PRIMARY KEY (`typId`)
    ) 
ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci COMMENT = 'Collects all types and subtypes for tracks, waypoints, segs';


-- ---------------------------------------------
-- SQL to create type/subtype entries for tracks
INSERT INTO `tbl_types` (`typId`, `typCode`, `typName`, `typParentId`, `typType`, `typPurpose`) 
VALUES 
    (NULL, 'ski', 'Ski', NULL, 'type', 'trk'), 
    (NULL, 'fuss', 'Zufuss', NULL, 'type', 'trk'),
    (NULL, 'velo', 'Velo', NULL, 'type', 'trk'),
    (NULL, 'wasser', 'Wasser', NULL, 'type', 'trk'),
    (NULL, 'klett', 'Klettern', NULL, 'type', 'trk'),
    (NULL, 'sport', 'Sport', NULL, 'type', 'trk');


INSERT INTO `tbl_types` (`typId`, `typCode`, `typName`, `typParentId`, `typType`, `typPurpose`) 
VALUES 
    (NULL, 'ski', 'Ski', NULL, 'type', 'trk'), 
    (NULL, 'fuss', 'Zufuss', NULL, 'type', 'trk'),
    (NULL, 'velo', 'Velo', NULL, 'type', 'trk'),
    (NULL, 'wasser', 'Wasser', NULL, 'type', 'trk'),
    (NULL, 'klett', 'Klettern', NULL, 'type', 'trk'),
    (NULL, 'sport', 'Sport', NULL, 'type', 'trk'),    
    (NULL, 'aklett', 'Alpinklettern', 5, 'stype', 'trk'),
    (NULL, 'msklett', 'Mehrseilklettern', 5, 'stype', 'trk'),
    (NULL, 'sklett', 'Sportklettern', 5, 'stype', 'trk'),
    (NULL, 'sht', 'Skihochtour', 1, 'stype', 'trk'),
    (NULL, 'st', 'Skitour', 1, 'stype', 'trk'),
    (NULL, 'ski', 'Alpinski', 6, 'stype', 'trk'),
    (NULL, 'jog', 'Joggen', 6, 'stype', 'trk'),
    (NULL, 'rvelo', 'Rennrad', 6, 'stype', 'trk'),
    (NULL, 'velo', 'Velotour', 3, 'stype', 'trk'),
    (NULL, 'schw', 'Schwimmen', 4, 'stype', 'trk'),
    (NULL, 'atour', 'Alpintour', 2, 'stype', 'trk'),
    (NULL, 'ht', 'Hochtour', 2, 'stype', 'trk'),
    (NULL, 'sst', 'Schneeschuhtour', 2, 'stype', 'trk'),
    (NULL, 'wa', 'Wanderung', 2, 'stype', 'trk'),
    (NULL, 'wiwa', 'Winterwanderung', 2, 'stype', 'trk');


-- SQL to create entries for segments (AW=atour, HT=ht, MK=msklett, ST=st, WA=wa)
INSERT INTO `tbl_types` (`typId`, `typCode`, `typName`, `typParentId`, `typType`, `typPurpose`)
VALUE
    (NULL, 'atour', 'Alpintour', NULL, 'type', 'seg'),
    (NULL, 'ht', 'Hochtour', NULL, 'type', 'seg'),
    (NULL, 'msklett', 'Mehrseilklettern', NULL, 'type', 'seg'),
    (NULL, 'st', 'Skitour', NULL, 'type', 'seg'),
    (NULL, 'wa', 'Wanderung', NULL, 'type', 'seg');


-- SQL to create entries for waypoints (1=bergstation, 2=tal, 3=waypoint, 4=hütte, 5=gipfel)
INSERT INTO `tbl_types` (`typId`, `typCode`, `typName`, `typParentId`, `typType`, `typPurpose`)
VALUE
    (NULL, 'bst', 'Bergstation', NULL, 'type', 'wayp'),
    (NULL, 'tal', 'Talort', NULL, 'type', 'wayp'),
    (NULL, 'wpt', 'Wegpunkt', NULL, 'type', 'wayp'),
    (NULL, 'hu', 'Hütte', NULL, 'type', 'wayp'),
    (NULL, 'gi', 'Gipfel', NULL, 'type', 'wayp');


-- ---------------------------------------
-- Add type / subtype fields to tbl_tracks

ALTER TABLE `tbl_tracks` ADD `trkTypeFid` INT(11) NOT NULL AFTER `trkDateFinish`, ADD `trkSubtypeFid` INT(11) NOT NULL AFTER `trkTypeFid`;
ALTER TABLE `tbl_segments` ADD `segTypeFid_new` INT(11) NOT NULL AFTER `segSourceRef`;
ALTER TABLE `tbl_waypoints` ADD `waypTypeFid_new` INT(11) NOT NULL AFTER `waypNameLong`;


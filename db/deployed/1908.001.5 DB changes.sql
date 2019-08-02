-- drop table with foreign key validation deactivated
DROP TABLE `tourdb2_prod`.`tbl_waypointtypes`;

ALTER TABLE tourdb2_prod.tbl_waypoints DROP FOREIGN KEY FK_tbl_waypoints_tbl_waypointTypes;

ALTER TABLE `tbl_waypoints` DROP INDEX `FK_tbl_waypoints_tbl_waypointTypes`;

ALTER TABLE `tbl_waypoints` CHANGE `waypTypeFid_new` `waypTypeFid` INT(11) NOT NULL, CHANGE `waypTypeFID` `waypTypeFID_old` INT(11) NULL DEFAULT NULL;
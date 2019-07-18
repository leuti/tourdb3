/* -------- */
/* tbl_part */

/* add user ID to part */ 
ALTER TABLE `tbl_part` ADD `partUsrId` INT(11) NOT NULL COMMENT 'ID of user creating the participant' AFTER `prtLastName`, ADD INDEX `partUsrId` (`partUsrId`);

/* change field FirstName to allow NULL values */
ALTER TABLE `tbl_part` CHANGE `prtFirstName` `prtFirstName` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Firstname';

/* update usrID in part table to 1 (LEUT) */
UPDATE `tbl_part` SET `partUsrId` = '1' WHERE 1

/* ---------- */
/* tbl_tracks */

/* add user ID to tracks */
ALTER TABLE `tbl_tracks` ADD `trkUsrId` INT(11) NOT NULL COMMENT 'ID of user creating the track' AFTER `trkLoginName`, ADD INDEX `trkUsrId` (`trkUsrId`);

/* update usrID in tracks table to 1 (LEUT) */
UPDATE `tbl_tracks` SET `trkUsrId` = '1' WHERE 1

/* Mane field trkLoginName not mandatory */
ALTER TABLE `tbl_tracks` CHANGE `trkLoginName` `trkLoginName` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;




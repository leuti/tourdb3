DROP TABLE `tourdb2_prod`.`tbl_segmenttypes`

ALTER TABLE tourdb2_prod.tbl_segments DROP FOREIGN KEY FK_tbl_segments_tbl_segmentTypes;

ALTER TABLE ``tbl_segments`` DROP INDEX ``FK_tbl_segments_tbl_segmentTypes``

ALTER TABLE `tbl_segments` DROP `segTypeFID`;

ALTER TABLE `tbl_segments` CHANGE `segTypeFid_new` `segTypeFid` INT(11) NOT NULL;


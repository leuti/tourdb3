ALTER TABLE `tbl_tracks` CHANGE `trkMeterUp` `trkMeterUp` DECIMAL(8,3) NULL DEFAULT NULL COMMENT 'Meters ascended';
ALTER TABLE `tbl_tracks` CHANGE `trkMeterDown` `trkMeterDown` DECIMAL(8,3) NULL DEFAULT NULL COMMENT 'Meters descended';
ALTER TABLE `tbl_tracks` CHANGE `trkDistance` `trkDistance` DECIMAL(7,3) NULL DEFAULT NULL COMMENT 'Distance in km';
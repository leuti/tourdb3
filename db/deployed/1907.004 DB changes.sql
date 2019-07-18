ALTER TABLE `tbl_tracks`
  DROP `trkLogbookId`,
  DROP `trkSourceFileName`,
  DROP `trkPeakRef`,
  DROP `trkDateBegin`,
  DROP `trkSaison`,
  DROP `trkOvernightLoc`,
  DROP `trkParticipants`,
  DROP `trkToReview`;

  
  ALTER TABLE `tbl_track_wayp`
  DROP `trwpWaypType`;


  ALTER TABLE `tbl_part`
  DROP `prtShort`;

  ALTER TABLE `tbl_tracks` CHANGE `trkGPSStartTime` `trkDateBegin` DATETIME NULL DEFAULT NULL COMMENT 'Content of GPX gpx->metadata->time>';
UPDATE `tourdb2`.`tbl_tracks` 
SET `trkTyp` = 'Sport'
	, `trkSubType` = 'Bike'
    , `trkCountry` = 'CH' 
WHERE `tbl_tracks`.`trkSourceFileName` like '%Ride%'
and isnull(trkCountry)

SELECT *
FROM `tbl_tracks`
WHERE `tbl_tracks`.`trkSourceFileName` like '%Run%'
and isnull(trkCountry)


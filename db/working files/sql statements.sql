SELECT tbl_track_wayp.trwpId
	, tbl_tracks.trkTrackName
	, tbl_tracks.trkId
	, tbl_waypoints.waypNameLong
    , tbl_waypoints.waypID
    , tbl_track_wayp.trwpWaypType
    , tbl_track_wayp.trwpReached_f
FROM `tbl_track_wayp`
INNER JOIN tbl_tracks ON tbl_track_wayp.trwpTrkId = tbl_tracks.trkId
INNER JOIN tbl_waypoints ON tbl_track_wayp.trwpWaypID = tbl_waypoints.waypID
where waypNameShort like '%chass%'
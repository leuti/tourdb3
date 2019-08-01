-- SQL to verify data for tracks
SELECT DISTINCT 
	  t.typCode
    , trk.trkType
    , st.typCode
    , trk.trkSubType
FROM tbl_tracks trk
INNER JOIN tbl_types t ON t.typId = trk.trkTypeFid
INNER JOIN tbl_types st on st.typId = trk.trkSubtypeFid

SELECT DISTINCT trkType, trkTypeFid
FROM `tbl_tracks` WHERE 1

-- SQL to verify data for segments
SELECT DISTINCT 
	  t.typCode
    , seg.segTypeFID
FROM tbl_segments seg
INNER JOIN tbl_types t ON t.typId = seg.segTypeFid_new


-- SQL to verify data for waypoints
SELECT DISTINCT 
	  t.typCode
    , wayp.waypTypeFID
FROM tbl_waypoints wayp
INNER JOIN tbl_types t ON t.typId = wayp.waypTypeFid_new


-- Update tracks with new trkTypeFid
UPDATE tbl_tracks SET trkTypeFid = 1 WHERE trkType = 'Ski';
UPDATE tbl_tracks SET trkTypeFid = 2 WHERE trkType = 'Zufuss';
UPDATE tbl_tracks SET trkTypeFid = 3 WHERE trkType = 'Velo';
UPDATE tbl_tracks SET trkTypeFid = 4 WHERE trkType = 'Wasser';
UPDATE tbl_tracks SET trkTypeFid = 5 WHERE trkType = 'Klettern';
UPDATE tbl_tracks SET trkTypeFid = 6 WHERE trkType = 'Sport';

-- Update tracks with new trkSubtypeFid
UPDATE tbl_tracks SET trkSubtypeFid = 17 WHERE trkSubType = 'Skitour';
UPDATE tbl_tracks SET trkSubtypeFid = 23 WHERE trkSubType = 'Alpintour';
UPDATE tbl_tracks SET trkSubtypeFid = 26 WHERE trkSubType = 'Wanderung';
UPDATE tbl_tracks SET trkSubtypeFid = 24 WHERE trkSubType = 'Hochtour';
UPDATE tbl_tracks SET trkSubtypeFid = 27 WHERE trkSubType = 'Winterwanderung';
UPDATE tbl_tracks SET trkSubtypeFid = 3 WHERE trkSubType = 'Velotour';
UPDATE tbl_tracks SET trkSubtypeFid = 25 WHERE trkSubType = 'Schneeschuhwanderung';
UPDATE tbl_tracks SET trkSubtypeFid = 16 WHERE trkSubType = 'Skihochtour';
UPDATE tbl_tracks SET trkSubtypeFid = 22 WHERE trkSubType = 'Schwimmen';
UPDATE tbl_tracks SET trkSubtypeFid = 14 WHERE trkSubType = 'Mehrseilklettern';
UPDATE tbl_tracks SET trkSubtypeFid = 13 WHERE trkSubType = 'Alpinklettern';
UPDATE tbl_tracks SET trkSubtypeFid = 15 WHERE trkSubType = 'Sportklettern';
UPDATE tbl_tracks SET trkSubtypeFid = 19 WHERE trkSubType = 'Joggen';
UPDATE tbl_tracks SET trkSubtypeFid = 20 WHERE trkSubType = 'Rennrad';
UPDATE tbl_tracks SET trkSubtypeFid = 1 WHERE trkSubType = 'Alpinski';


-- Update segments with segTypeFid_new
UPDATE tbl_segments SET segTypeFid_new = 23 WHERE segTypeFID =  'AW';
UPDATE tbl_segments SET segTypeFid_new = 24 WHERE segTypeFID =  'HT';
UPDATE tbl_segments SET segTypeFid_new = 14 WHERE segTypeFID =  'MK';
UPDATE tbl_segments SET segTypeFid_new = 17 WHERE segTypeFID =  'ST';
UPDATE tbl_segments SET segTypeFid_new = 26 WHERE segTypeFID =  'WA';


-- Update waypoints with segTypeFid_new
UPDATE tbl_waypoints SET waypTypeFid_new = 33 WHERE waypTypeFID =  '1';
UPDATE tbl_waypoints SET waypTypeFid_new = 34 WHERE waypTypeFID =  '2';
UPDATE tbl_waypoints SET waypTypeFid_new = 35 WHERE waypTypeFID =  '3';
UPDATE tbl_waypoints SET waypTypeFid_new = 36 WHERE waypTypeFID =  '4';
UPDATE tbl_waypoints SET waypTypeFid_new = 37 WHERE waypTypeFID =  '5';

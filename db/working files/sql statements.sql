SELECT  tbl_waypoints.waypName
        , s1.trkId
        , s1.trkLoginName
        , s1.trwpWaypID
        , s1.saison
FROM 
(
SELECT tbl_tracks.trkId
		,tbl_tracks.trkLoginName
        ,tbl_track_wayp.trwpWaypID
        , CONCAT 
        ( 
            CASE tbl_tracks.trkSubType
                WHEN 'Alpinklettern' THEN 'So'
                WHEN 'Alpintour' THEN 'So'
                WHEN 'Hochtour' THEN 'So'
                WHEN 'Joggen' THEN 'So'
                WHEN 'Mehrseilklettern' THEN 'So'
                WHEN 'Sportklettern' THEN 'So'
                WHEN 'Velotour' THEN 'So'
                WHEN 'Wanderung' THEN 'So'
                ELSE 'na'
            END 
            , '|'
            , CASE tbl_tracks.trkSubType
                WHEN 'Schneeschuhwanderung' THEN 'Wi'
                WHEN 'Skihochtour' THEN 'Wi'
                WHEN 'Skitour' THEN 'Wi'
                WHEN 'Winterwanderung' THEN 'Wi'
                ELSE 'na'
            END 
        ) as 'saison'

    FROM tbl_tracks
    RIGHT JOIN tbl_track_wayp on tbl_tracks.trkId = tbl_track_wayp.trwpTrkId
    WHERE tbl_track_wayp.trwpWaypID IS NOT Null
        AND tbl_track_wayp.trwpReached_f = 1
    GROUP BY trkId, trkLoginName, trwpWaypID, saison
) AS s1
LEFT JOIN tbl_waypoints ON tbl_waypId = s1.trwpWaypID



SELECT  tbl_waypoints.waypNameLong
        , s1.trkLoginName
        , s1.trwpWaypID
        , s1.saison
FROM 
(
SELECT tbl_track_wayp.trwpWaypID
        , tbl_tracks.trkLoginName
        , CASE 
            WHEN tbl_tracks.trkSubType = 'Alpinklettern' THEN 1000
            WHEN tbl_tracks.trkSubType = 'Alpintour' THEN 1000
            WHEN tbl_tracks.trkSubType = 'Hochtour' THEN 1000
            WHEN tbl_tracks.trkSubType = 'Joggen' THEN 1000
            WHEN tbl_tracks.trkSubType = 'Mehrseilklettern' THEN 1000
            WHEN tbl_tracks.trkSubType = 'Sportklettern' THEN 1000
            WHEN tbl_tracks.trkSubType = 'Velotour' THEN 1000
            WHEN tbl_tracks.trkSubType = 'Wanderung' THEN 1000
            WHEN tbl_tracks.trkSubType = 'Schneeschuhwanderung' THEN 1
            WHEN tbl_tracks.trkSubType = 'Skihochtour' THEN 1
            WHEN tbl_tracks.trkSubType = 'Skitour' THEN 1
            WHEN tbl_tracks.trkSubType = 'Winterwanderung' THEN 1
            ELSE 0
        END as 'saison'

    FROM tbl_tracks
    RIGHT JOIN tbl_track_wayp on tbl_tracks.trkId = tbl_track_wayp.trwpTrkId
    WHERE tbl_track_wayp.trwpWaypID IS NOT Null
        AND tbl_track_wayp.trwpReached_f = 1
    GROUP BY trwpWaypID, trkLoginName, saison

) AS s1
LEFT JOIN tbl_waypoints ON tbl_waypoints.waypID = s1.trwpWaypID



SELECT  tbl_waypoints.waypNameLong
        , s1.trkLoginName
        , s1.trwpWaypID
        , sum(s1.saison)
FROM 
(
SELECT tbl_track_wayp.trwpWaypID
        , tbl_tracks.trkLoginName
        , CASE tbl_tracks.trkSubType
            WHEN 'Alpinklettern' THEN 1000
            WHEN 'Alpintour' THEN 1000
            WHEN 'Hochtour' THEN 1000
            WHEN 'Joggen' THEN 1000
            WHEN 'Mehrseilklettern' THEN 1000
            WHEN 'Sportklettern' THEN 1000
            WHEN 'Velotour' THEN 1000
            WHEN 'Wanderung' THEN 1000
            WHEN 'Schneeschuhwanderung' THEN 1
            WHEN 'Skihochtour' THEN 1
            WHEN 'Skitour' THEN 1
            WHEN 'Winterwanderung' THEN 1
            ELSE 0
        END as 'saison'

    FROM tbl_tracks
    RIGHT JOIN tbl_track_wayp on tbl_tracks.trkId = tbl_track_wayp.trwpTrkId
    
    WHERE tbl_track_wayp.trwpWaypID IS NOT Null
        AND tbl_track_wayp.trwpReached_f = 1
) AS s1
LEFT JOIN tbl_waypoints ON tbl_waypoints.waypID = s1.trwpWaypID
GROUP BY waypNameLong, trkLoginName, trwpWaypID


-- saison 
-- 0 --> none
-- between 1 and 999 --> Winter
-- saison / 1000 ==> ungerade Zahl --> Sommer und Winter



SELECT tbl_waypoints.waypID
    , tbl_waypoints.waypNameLong
    , tbl_waypoints.waypTypeFID
    , tbl_waypoints.waypAltitude
    , tbl_waypoints.waypCoordWGS84E
    , tbl_waypoints.waypCoordWGS84N
    , s1.trkLoginName
    , s1.trwpWaypID
    , sum(s1.saison) 
FROM (
    SELECT tbl_track_wayp.trwpWaypID
        , tbl_tracks.trkId
        , tbl_tracks.trkLoginName
        , CASE tbl_tracks.trkSubType WHEN 'Alpinklettern' THEN 1000 WHEN 'Alpintour' THEN 1000 WHEN 'Hochtour' THEN 1000 WHEN 'Joggen' THEN 1000 WHEN 'Mehrseilklettern' THEN 1000 WHEN 'Sportklettern' THEN 1000 WHEN 'Velotour' THEN 1000 WHEN 'Wanderung' THEN 1000 WHEN 'Schneeschuhwanderung' THEN 1 WHEN 'Skihochtour' THEN 1 WHEN 'Skitour' THEN 1 WHEN 'Winterwanderung' THEN 1 ELSE 0 END 
            as 'saison' 
        , tbl_track_wayp.trwpReached_f 
    FROM tbl_tracks 
    JOIN tbl_track_wayp on tbl_tracks.trkId = tbl_track_wayp.trwpWaypId
    WHERE tbl_tracks.trkLoginName = 'leut'
) AS s1 
RIGHT JOIN tbl_waypoints ON s1.trwpWaypID = tbl_waypoints.waypID
WHERE tbl_waypoints.waypTypeFID = 5
GROUP BY waypID, waypNameLong, waypTypeFID, waypAltitude, waypCoordWGS84E, waypCoordWGS84N, trkLoginName, s1.trwpWaypID

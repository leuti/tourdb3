-- Number of tracks per year
SELECT YEAR(trkDateBegin), COUNT(1)
FROM tbl_tracks 
WHERE trkType in ('Zufuss','Ski','Klettern')
GROUP BY YEAR(trkDateBegin);

-- 4000er per year
SELECT YEAR(trkDateBegin)
	, CONCAT ( wp.waypNameLong, " (", wp.waypAltitude, "m)" )
FROM tbl_track_wayp trwp
INNER JOIN tbl_waypoints wp ON wp.waypID = trwp.trwpWaypID
INNER JOIN tbl_tracks tr ON tr.trkId = trwp.trwpTrkId
WHERE wp.waypAltitude >= 4000
ORDER BY trkDateBegin DESC;


-- 4000 climbed / not climbed 
-- Issue: when a peak has track with reached_f = false and also reached_f = true it will appear twice
SELECT DISTINCT CONCAT ( wp.waypNameLong, " (", wp.waypAltitude, "m)" )
	 , CASE 
     	WHEN trwp.trwpReached_f = 1 THEN "done"
     	WHEN trwp.trwpReached_f is null	THEN "open"
        ELSE "open"
       END AS "status"
FROM tbl_waypoints wp
LEFT OUTER JOIN ( SELECT trwp.* FROM tbl_track_wayp trwp WHERE trwp.trwpReached_f = 1 ) trwp ON trwp.trwpWaypID = wp.waypID
WHERE wp.waypAltitude >= 4000
AND wp.waypCountry LIKE '%CH%'
AND wp.waypUIAA4000 = 1
ORDER BY status DESC, wp.waypAltitude DESC

-- Top of canton climbed / not climed
SELECT DISTINCT CONCAT ( wp.waypNameLong, " (", wp.waypAltitude, "m)" )
	 , CASE 
     	WHEN trwp.trwpReached_f = 1 THEN "done"
     	WHEN trwp.trwpReached_f is null	THEN "open"
        ELSE "open"
       END AS "status"
FROM tbl_waypoints wp
LEFT OUTER JOIN ( SELECT trwp.* FROM tbl_track_wayp trwp WHERE trwp.trwpReached_f = 1 ) trwp ON trwp.trwpWaypID = wp.waypID
WHERE wp.waypToOfCant IS NOT null AND wp.waypToOfCant <> '0' AND wp.waypTypeFID = 5
ORDER BY status DESC, wp.waypAltitude DESC


-- Top 10 tracks by distance
SELECT tr.trkTrackName, tr.trkDistance
FROM tbl_tracks tr
WHERE tr.trkType = "Zufuss"
ORDER BY tr.trkDistance DESC
LIMIT 5;

-- Top 10 tracks by overall time
SELECT tr.trkTrackName, tr.trkTimeOverall
FROM tbl_tracks tr
WHERE tr.trkType = "Zufuss"
ORDER BY tr.trkTimeOverall DESC
LIMIT 10;



-- Trying to select top N tracks per year / type
SELECT city, country, population
   FROM
     (SELECT city, country, population, 
                  @country_rank := IF(@current_country = country, @country_rank + 1, 1) AS country_rank,
                  @current_country := country 
       FROM cities
       ORDER BY country, population DESC
     ) ranked
   WHERE country_rank <= 2;
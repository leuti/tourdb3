-- Select statement to test
SELECT * FROM `tbl_waypoints` WHERE waypNameLong not like '%|%' AND waypTypeFID =  5

-- Update statement
UPDATE tbl_waypoints
SET waypNameLong = CONCAT( waypNameLong, " (", waypAltitude, "m|", waypCanton, ")")
WHERE waypNameLong not like '%|%' AND waypTypeFID =  5


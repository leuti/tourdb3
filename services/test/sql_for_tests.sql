SELECT  trkId
        , trkLogbookId
        , trkSourceFileName
        , trkPeakRef
        , trkDateBegin
        , trkDateFinish
        , trkGPSStartTime
        , trkSaison
        , trkTyp
        , trkSubType
        , trkOrg
        , trkTrackName
        , trkRoute
        , trkOvernightLoc
        , trkParticipants
        , trkEvent
        , trkRemarks
        , trkDistance
        , trkTimeOverall
        , trkTimeToTarget
        , trkTimeToEnd
        , trkGrade
        , trkMeterUp
        , trkMeterDown
        , trkToReview
        , tp.tptTrackFID
        , count(tp.tptTrackFID)
from tbl_trackpoints tp
inner join tbl_tracks t on t.trkId = tp.tptTrackFID

group by tp.tptTrackFID
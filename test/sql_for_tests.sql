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
        , trkCountry
        , count(tp.tptTrackFID)
from tbl_trackpoints tp
outer left join tbl_tracks t on t.trkId = tp.tptTrackFID

group by tp.tptTrackFID
order by trkId
<?php
App::uses('TableSnapshot', 'Model');
class TrackReviewSnapshot extends TableSnapshot
{	    
	public $name        = 'TrackReviewSnapshot';
    public $useTable    = 'track_review_snapshots';  
    public $hasMany     = array('ReviewFrames');
    public $belongsTo   = array('Track');            
    
      
    public function getCurve($trackId, $resolution = 100, $timestamp = 0)
    {
        $trackInfo  = $this->Track->find("first", array("fields" => array("duration"), "conditions" => array("Track.id" => $trackId)));        
        $curveData  = $this->getRawCurveData($timestamp);
        $ppf        = $this->resolutionToPositionsPerFrames((int)$trackInfo["Track"]["duration"], $resolution);
        
        return array(
            "ppf"   => $ppf,
            "curve" => $this->ReviewFrames->roundReviewFramesSpan($curveData, $ppf, $resolution),
            "score" => $this->compileScore($curveData)
        );
    }
    
    public function getTopAreasByUserId($userId, $range = 15)
    {
        $data = $this->ReviewFrames->getTopPositions("user_id = $userId");        
        $trackData = array();                        
        
        foreach($data as $idx => $bestFrame)
        {   
            $start  = $bestFrame["TopAreas"]["position"] - $range;
            $end    = $bestFrame["TopAreas"]["position"] + $range;
            $infos  = $this->getRawCurveSpan(array(
                "track_id"      => $bestFrame["TopAreas"]["track_id"],
                "user_id"       => $userId,
                "position >="   => $start,
                "position <="   => $end
            ));                                
            
            $trackInfo = $this->Track->find("first", array(
                "conditions" => array("Track.id" => $bestFrame["TopAreas"]["track_id"]),
                "contain" => array("Album" => array( "Artist" ))
            ));
                        
            $ppf    = $this->resolutionToPositionsPerFrames(count($infos), $range*2);
            $trackData[] = array_merge($trackInfo, array(
                "snapshot" => array(
                    "start" => $start > 0 ? $start : 0,
                    "end"   => $end > $trackInfo["Track"]["duration"] ? $trackInfo["Track"]["duration"] : $end,
                    "snapshot_ppf"   => $ppf,
                    "curve_snapshot" => $this->ReviewFrames->roundReviewFramesSpan($infos, $ppf, count($infos)),                
                    "score_snapshot" => $this->compileScore($infos)
                )
            ));
        }
        
        return $trackData;
    }    
    
    public function getRecentReviewsByUserId($userId, $limit)
    {
        return $this->getRecentReviews(array("user_id" => $userId), $limit);        
    }
}
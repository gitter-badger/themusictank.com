<?php
App::uses('UserReviewSnapshot', 'Model');
App::uses('CakeSession', 'Model/Datasource');   

class UserAlbumReviewSnapshot extends UserReviewSnapshot
{	    
	public $name        = 'UserAlbumReviewSnapshot';
    public $useTable    = 'user_album_review_snapshots';  
    public $belongsTo   = array('Album', 'User');
       
    
    public function getCurve($albumId, $resolution = 100, $timestamp = 0)
    {
        $albumInfo = $this->Album->find("first", array("conditions" => array("Album.id" => $albumId)));
        $curveData = $this->getRawCurveData($timestamp); 
        $curve = array();
        $splitMin = array();
        $splitMax = array();
        $score      = $this->compileScore($curveData);      
        $splitData      = $this->getRawSplitData($score, $timestamp);
        $review     = new ReviewFrames();
        
        foreach($albumInfo["Tracks"] as $track)
        {
            $trackResolution    =  ((int)$track["duration"] / (int)$albumInfo["Album"]["duration"]) * $resolution;
            $ppf                = $this->resolutionToPositionsPerFrames((int)$track["duration"], $trackResolution);
            //$curve[$track["title"]] = (new ReviewFrames())->roundReviewFramesSpan( $this->_getTrackCurveSpan($curveData, $track["id"]), $ppf, $trackResolution);
            $curve = array_merge($curve, $review->roundReviewFramesSpan( $this->_getTrackCurveSpan($curveData, $track["id"]), $ppf, $trackResolution));
            $splitMin = array_merge($splitMin, $review->roundReviewFramesSpan( $this->_getTrackCurveSpan($splitData["min"], $track["id"]), $ppf, $trackResolution));
            $splitMax = array_merge($splitMax, $review->roundReviewFramesSpan( $this->_getTrackCurveSpan($splitData["max"], $track["id"]), $ppf, $trackResolution));
        }
        
        return array(
            "curve"         => $curve, 
            "ppf"           => $this->resolutionToPositionsPerFrames((int)$albumInfo["Album"]["duration"], $resolution),
            "score"         => $score,
            "split" => array(
                "min" => $splitMin,
                "max" => $splitMax
            )
        );
    }
    
    
    private function _getTrackCurveSpan($reviewFrames, $trackId)
    {
        $startIdx = null;
        $count = 0;
        foreach($reviewFrames as $idx => $frame)
        {            
            if(is_null($startIdx) && $frame["ReviewFrames"]["track_id"] === $trackId)
            {
                $startIdx = $idx;
            }
            
            if($startIdx >= 0 && $frame["ReviewFrames"]["track_id"] === $trackId)
            {
                $count++;
            }
        }
        
        return ($count > 0) ?
            array_slice($reviewFrames, $startIdx, $count) :
            array();
    }
    
}
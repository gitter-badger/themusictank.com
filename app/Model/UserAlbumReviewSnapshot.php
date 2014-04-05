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
        $albumInfo  = $this->Album->find("first", array("conditions" => array("Album.id" => $albumId)));
        $id         = CakeSession::read('Auth.User.User.id');   
        $curveData  = $this->getRawCurveData($timestamp, array("ReviewFrames.user_id" => $id));        
        $score      = $this->compileScore($curveData);      
        $splitData  = $this->getRawSplitData($score, $timestamp);
        $albumDuration = (int)$albumInfo["Album"]["duration"];
        
        $curve      = array();
        $range      = array("min" => array(), "max" => array());
        
        foreach($albumInfo["Tracks"] as $track)
        {
            $trackResolution    =  ((int)$track["duration"] / $albumDuration) * $resolution;
            $ppf                = ReviewFrames::resolutionToPositionsPerFrames((int)$track["duration"], $trackResolution);
            
            // Handle curve
            $trackCurve         = ReviewFrames::getTrackSpan($curveData, $track["id"]);
            $curve              = array_merge($curve, ReviewFrames::lowerSpanResolution($trackCurve, $ppf, $trackResolution));
                        
            // Handle ranges
            $trackSplitMin      = ReviewFrames::getTrackSpan($splitData["min"], $track["id"]);
            $trackSplitMax      = ReviewFrames::getTrackSpan($splitData["max"], $track["id"]);
            $range["min"]       = array_merge($range["min"], ReviewFrames::lowerSpanResolution($trackSplitMin, $ppf, $trackResolution));
            $range["max"]       = array_merge($range["max"], ReviewFrames::lowerSpanResolution($trackSplitMax, $ppf, $trackResolution));
        }
        
        return array(
            "curve" => $curve, 
            "ppf"   => ReviewFrames::resolutionToPositionsPerFrames($albumDuration, $resolution),
            "score" => $score,
            "split" => $range
        );
    } 
    
    public function getAppreciation($belongsToId, $timestamp = 0, $extraConditions = null)
    {                
        return parent::getappreciation($belongsToId, $timestamp, "user_id = " . CakeSession::read('Auth.User.User.id'));
    }
}
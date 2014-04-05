<?php
App::uses('UserReviewSnapshot', 'Model');
App::uses('CakeSession', 'Model/Datasource');   

class SubscribersAlbumReviewSnapshot extends UserReviewSnapshot
{	    
	public $name        = 'SubscribersAlbumReviewSnapshot';
    public $useTable    = 'subscribers_album_review_snapshots';  
    public $belongsTo   = array('Album', 'User');
    
    public function getCurve($albumId, $resolution = 100, $timestamp = 0)
    {        
        $albumInfo  = $this->Album->findById($albumId);
        $ids        = $this->User->UserFollowers->getSubscriptions(CakeSession::read('Auth.User.User.id'), true);   
        $curveData  = $this->getRawCurveData($timestamp, array("ReviewFrames.user_id" => $ids));
        $score      = $this->compileScore($curveData); 
        $splitData  = $this->getRawSplitData($score, $timestamp, array("ReviewFrames.user_id" => $ids));
        $albumDuration = (int)$albumInfo["Album"]["duration"];
        
        $curve = array();
        $split = array("min" => array(), "max" => array());
        
        foreach($albumInfo["Tracks"] as $track)
        {
            $trackResolution    = ((int)$track["duration"] / $albumDuration) * $resolution;
            $ppf                = ReviewFrames::resolutionToPositionsPerFrames((int)$track["duration"], $trackResolution);
            
            // Handle curve
            $trackCurve         = ReviewFrames::getTrackSpan($curveData, $track["id"]);
            $curve              = array_merge($curve, ReviewFrames::lowerSpanResolution($trackCurve, $ppf, $trackResolution));
           
            // Handle ranges
            $trackSplitMin      = ReviewFrames::getTrackSpan($splitData["min"], $track["id"]);
            $trackSplitMax      = ReviewFrames::getTrackSpan($splitData["max"], $track["id"]);
            $split["min"]       = array_merge($split["min"], ReviewFrames::lowerSpanResolution($trackSplitMin, $ppf, $trackResolution));
            $split["max"]       = array_merge($split["max"], ReviewFrames::lowerSpanResolution($trackSplitMax, $ppf, $trackResolution));
        }
                
        return array(
            "curve" => $curve, 
            "ppf"   => ReviewFrames::resolutionToPositionsPerFrames($albumDuration, $resolution),
            "score" => $score,
            "split" => $split
        );
    }    
    
    
    public function getAppreciation($belongsToId, $timestamp = 0, $extraConditions = null)
    {                
        $ids            = $this->User->UserFollowers->getSubscriptions(CakeSession::read('Auth.User.User.id'), true);   
        $extraConditions=  (count($ids) > 0) ? "user_id IN (" . implode(",", $ids) . ")" : "0 = 1";
        return parent::getappreciation($belongsToId, $timestamp, $extraConditions);
    }       
}
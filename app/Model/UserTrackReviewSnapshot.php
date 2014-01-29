<?php
App::uses('UserReviewSnapshot', 'Model');
App::uses('CakeSession', 'Model/Datasource');   

class UserTrackReviewSnapshot extends UserReviewSnapshot
{	    
	public $name        = 'UserTrackReviewSnapshot';
    public $useTable    = 'user_track_review_snapshots';  
    public $belongsTo   = array('Track', 'User');
 
    public function getByTrackId($trackId)
    {
        return $this->getByBelongsToId($trackId);
    }    
            
    public function getCurve($trackId, $resolution = 100, $timestamp = 0)
    {             
        $belongsToAlias = $this->getBelongsToAlias();    
        $ids        = $this->User->UserFollowers->getSubscriptions(CakeSession::read('Auth.User.User.id'), true);   
        $curveData  = $this->getRawCurveData($timestamp, array("ReviewFrames.user_id" => $ids));
        $data       = $this->Track->find("first", array("fields" => array("duration"), "conditions" => array("Track.id" => $trackId)));
        $ppf        = $this->resolutionToPositionsPerFrames((int)$data[$belongsToAlias]["duration"], $resolution);
        $score      = $this->compileScore($curveData);      
        $split      = $this->getRawSplitData($score, $timestamp, array("ReviewFrames.user_id" => $ids));
        $review     = new ReviewFrames();
        
        
        return array(
            "ppf"   => $ppf,    
            "curve" => $review->roundReviewFramesSpan($curveData, $ppf, $resolution),
            "score" => $score,
            "split" => array(
                "min" => $review->roundReviewFramesSpan($split["min"], $ppf, $resolution),
                "max" => $review->roundReviewFramesSpan($split["max"], $ppf, $resolution)
            )
        );
    }
    
}
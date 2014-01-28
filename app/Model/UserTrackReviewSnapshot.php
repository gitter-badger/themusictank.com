<?php
App::uses('TableSnapshot', 'Model');
App::uses('CakeSession', 'Model/Datasource');   

class UserTrackReviewSnapshot extends TableSnapshot
{	    
	public $name        = 'UserTrackReviewSnapshot';
    public $useTable    = 'user_track_review_snapshots';  
    public $belongsTo   = array('Track', 'User');
              
    public function requiresUpdate($data = null)
    {        
        if(!is_null($data)) $this->data = $data;
        $userId = CakeSession::read('Auth.User.User.id');
        if($userId) return $this->_isExpired($userId, $data["Track"]["id"]);
        return false;
    }    
        
    public function getCurve($trackId, $resolution = 100, $timestamp = 0)
    {        
        $ids        = $this->User->UserFollowers->getSubscriptions(CakeSession::read('Auth.User.User.id'), true);
        $trackInfo  = $this->Track->find("first", array("fields" => array("duration"), "conditions" => array("Track.id" => $trackId)));        
        $curveData  = $this->getRawCurveData($timestamp, array("ReviewFrames.user_id" => $ids));
        $ppf        = $this->resolutionToPositionsPerFrames((int)$trackInfo["Track"]["duration"], $resolution);
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
    
    
    public function getBelongsToData()
    {
        return array(
            "id"  => $this->data["Track"]["id"]
        );
    }
    
    public function getByTrackId($trackId)
    {
         $data = $this->find("first", array(
            "conditions" => array(
                "user_id"   => CakeSession::read('Auth.User.User.id'), 
                "track_id"  => $trackId,
                "lastsync > " => time() - (HOUR*12)
            ),
            "fields" => array("UserTrackReviewSnapshot.*")
        ));
         
        return $data["UserTrackReviewSnapshot"];
    }    
    
    public function getExtraSaveFields()
    {
        $userId = CakeSession::read('Auth.User.User.id');
        $trackId = $this->data["Track"]["id"];
        
        $extras = array(
            "lastsync"  => time(),
            "user_id"   => $userId,
            "track_id"  => $trackId
        );
                
        $data = $this->_getId($userId, $trackId);        
        if($data)
        {            
           $extras["id"] = $data["UserTrackReviewSnapshot"]["id"];
        }
                
        return $extras;
    }
        
    protected function _getId($userId, $trackId)
    {
        return $this->find("first", array(
            "conditions" => array(
                "user_id"   => $userId, 
                "track_id"  => $trackId
            ),
            "fields" => array("id")
        ));
    }
    
    protected function _isExpired($userId, $trackId)
    {
        return !$this->find("count", array(
            "conditions" => array(
                "user_id"   => $userId, 
                "track_id"  => $trackId,
                "lastsync > " => time() - (HOUR*12)
            )
        )) > 0;
    }
    
}
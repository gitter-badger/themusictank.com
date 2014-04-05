<?php
App::uses('UserReviewSnapshot', 'Model');
App::uses('CakeSession', 'Model/Datasource');   

class UserArtistReviewSnapshot extends UserReviewSnapshot
{	    
	public $name        = 'UserArtistReviewSnapshot';
    public $useTable    = 'user_artist_review_snapshots';  
    public $belongsTo   = array('Artist', 'User');
          
    public function getByArtistId($artistId)
    {
        return $this->getByBelongsToId($artistId);
    }
        
    public function getCurve($artistId, $resolution = 100, $timestamp = 0)
    {
        $curveData  = $this->getRawCurveData($timestamp); 
        $score      = $this->compileScore($curveData);     
        return array(
            "score"         => $score
        );
    }
    
}
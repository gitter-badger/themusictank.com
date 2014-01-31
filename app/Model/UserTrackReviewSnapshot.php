<?php
App::uses('UserReviewSnapshot', 'Model');
App::uses('CakeSession', 'Model/Datasource');   

class UserTrackReviewSnapshot extends UserReviewSnapshot
{	    
	public $name        = 'UserTrackReviewSnapshot';
    public $useTable    = 'user_track_review_snapshots';  
    public $belongsTo   = array('Track', 'User');
             
    public function getCurve($trackId, $resolution = 100, $timestamp = 0)
    {             
        $belongsToAlias = $this->getBelongsToAlias();    
        $id         = CakeSession::read('Auth.User.User.id');   
        $curveData  = $this->getRawCurveData($timestamp, array("ReviewFrames.user_id" => $id));
        $data       = $this->Track->find("first", array("fields" => array("duration"), "conditions" => array("Track.id" => $trackId)));
        $ppf        = ReviewFrames::resolutionToPositionsPerFrames((int)$data[$belongsToAlias]["duration"], $resolution);
        $score      = $this->compileScore($curveData);      
        $split      = $this->getRawSplitData($score, $timestamp, array("ReviewFrames.user_id" => $id));
                
        return array(
            "ppf"   => $ppf,    
            "curve" => ReviewFrames::lowerSpanResolution($curveData, $ppf, $resolution),
            "score" => $score,
            "split" => array(
                "min" => ReviewFrames::lowerSpanResolution($split["min"], $ppf, $resolution),
                "max" => ReviewFrames::lowerSpanResolution($split["max"], $ppf, $resolution)
            )
        );
    }
    
    public function getAppreciation($belongsToId, $timestamp = 0, $extraConditions = null)
    {                
        $belongsToAlias = strtolower($this->getBelongsToAlias() . "_id");
        $id         = CakeSession::read('Auth.User.User.id');   
        return (new ReviewFrames())->getAppreciation("$belongsToAlias = $belongsToId AND created > $timestamp AND user_id = $id");
    }       
    
}
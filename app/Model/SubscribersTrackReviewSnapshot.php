<?php
App::uses('TableSnapshot', 'Model');
App::uses('CakeSession', 'Model/Datasource');   

class SubscribersTrackReviewSnapshot extends TableSnapshot
{	    
	public $name        = 'SubscribersTrackReviewSnapshot';
    public $useTable    = 'subscribers_track_review_snapshots';  
    public $belongsTo   = array('Track', 'User');

    public function getCurve($trackId, $resolution = 100, $timestamp = 0)
    {             
        $ids        = $this->User->UserFollowers->getSubscriptions(CakeSession::read('Auth.User.User.id'), true);   
        $data       = $this->Track->find("first", array("fields" => array("duration"), "conditions" => array("Track.id" => $trackId)));
        $ppf        = ReviewFrames::resolutionToPositionsPerFrames((int)$data["Track"]["duration"], $resolution);
        
        $curveData  = $this->getRawCurveData($timestamp, array("ReviewFrames.user_id" => $ids));
        $score      = $this->compileScore($curveData);      
        $split      = $this->getRawSplitData($score, $timestamp, array("ReviewFrames.user_id" => $ids));        
        
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
        $ids = $this->User->UserFollowers->getSubscriptions(CakeSession::read('Auth.User.User.id'), true);
        $extraConditions=  (count($ids) > 0) ? "user_id IN (" . implode(",", $ids) . ")" : "0 = 1";
        return parent::getappreciation($belongsToId, $timestamp, $extraConditions);
    }


    public function fetch($trackId, $userIds) {

        $conditions = array();
        $conditions = Hash::insert($conditions, "track_id", $trackId);
        $conditions = Hash::insert($conditions, "user_id", $userIds);

        return $this->updateCached( $conditions );
    }
    
}
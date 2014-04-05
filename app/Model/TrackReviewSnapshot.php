<?php
App::uses('TableSnapshot', 'Model');
class TrackReviewSnapshot extends TableSnapshot
{	    
	public $name        = 'TrackReviewSnapshot';
    public $useTable    = 'track_review_snapshots';  
    public $belongsTo   = array('Track');            
    
      
    public function getCurve($trackId, $resolution = 100, $timestamp = 0)
    {
        $trackInfo  = $this->Track->find("first", array("fields" => array("duration"), "conditions" => array("Track.id" => $trackId)));        
        $curveData  = $this->getRawCurveData($timestamp);
        $ppf        = ReviewFrames::resolutionToPositionsPerFrames((int)$trackInfo["Track"]["duration"], $resolution);
        $score      = $this->compileScore($curveData);      
        $split      = $this->getRawSplitData($score, $timestamp);
        
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
}
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
        $curveData = $this->getRawCurveData($timestamp); 
        $score      = $this->compileScore($curveData);     
        return array(
            "score"         => $score
        );
        
        /*
        $albumInfo = $this->Album->find("first", array("conditions" => array("Album.id" => $artistId), "fields" => array("Album.duration", "Tracks.id", "Tracks.duration")));
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
        );*/
    }
    
}
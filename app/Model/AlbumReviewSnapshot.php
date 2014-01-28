<?php

App::uses('TableSnapshot', 'Model');
class AlbumReviewSnapshot extends TableSnapshot
{	    
	public $name        = 'AlbumReviewSnapshot';
    public $useTable    = 'album_review_snapshots'; 
    public $belongsTo   = array('Album');
    
    public function getCurve($albumId, $resolution = 100, $timestamp = 0)
    {
        $albumInfo = $this->Album->find("first", array("conditions" => array("Album.id" => $albumId)));
        $curveData = $this->getRawCurveData($timestamp); 
        $curve = array();
        $score      = $this->compileScore($curveData);      
        $split      = $this->getRawSplitData($score, $timestamp);
        $review     = new ReviewFrames();
        
        foreach($albumInfo["Tracks"] as $track)
        {
            $trackResolution    =  ((int)$track["duration"] / (int)$albumInfo["Album"]["duration"]) * $resolution;
            $ppf                = $this->resolutionToPositionsPerFrames((int)$track["duration"], $trackResolution);
            $curve[$track["title"]] = (new ReviewFrames())->roundReviewFramesSpan( $this->_getTrackCurveSpan($curveData, $track["id"]), $ppf, $trackResolution);
        }
        
        return array(
            "curve"         => json_encode($curve), 
            "ppf"           => $this->resolutionToPositionsPerFrames((int)$albumInfo["Album"]["duration"], $resolution),
            "score"         => $score,
            "split" => array(
                "min" => $review->roundReviewFramesSpan($split["min"], $ppf, $resolution),
                "max" => $review->roundReviewFramesSpan($split["max"], $ppf, $resolution)
            )
        );
    }
        
    public function getExtraSaveFields()
    {   
        $saveArray = parent::getExtraSaveFields();
        
        $albumName = $this->_toMetacriticLabel($this->data["Album"]["name"]);
        $artistName = $this->_toMetacriticLabel($this->data["Artist"]["name"]);        
        $saveArray["metacritic_score"] = $this->_getMetacriticScore($albumName, $artistName);
        
        return $saveArray; 
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
    
    private function _toMetacriticLabel($string)
    {
        return strtolower(Inflector::slug($string,'-'));
    }
    
    private function _getMetacriticScore($albumTitle, $artistName)
    {
        try
        {
            $doc = new DOMDocument();
            @$doc->loadHTMLFile('http://www.metacritic.com/music/' . $albumTitle . '/' . $artistName);

            foreach($doc->getElementsByTagName("div") as $div)
            {
                if(preg_match('/^metascore_w xlarge/', $div->getAttribute("class")))
                {
                    foreach($div->getElementsByTagName("span") as $span)
                    {
                        // By convention, all TMT percentages are smaller than 1.
                        return (int)$span->nodeValue / 100;
                    }
                }
            }
        }
        catch (Exception $ex)
        {}
        
        return null;
        
    }
}
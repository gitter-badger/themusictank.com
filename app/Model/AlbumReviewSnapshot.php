<?php

App::uses('TableSnapshot', 'Model');
class AlbumReviewSnapshot extends TableSnapshot
{	    
	public $name        = 'AlbumReviewSnapshot';
    public $useTable    = 'album_review_snapshots';  
    public $hasMany     = array('ReviewFrames');
    public $belongsTo   = array('Album');
    
    public function getCurve($albumId, $resolution = 100, $timestamp = 0)
    {
        $albumInfo = $this->Album->find("first", array("conditions" => array("Album.id" => $albumId)));
        $curveData = $this->getRawCurveData($timestamp); 
        $curve = array();
        
        foreach($albumInfo["Tracks"] as $track)
        {
            $trackResolution    =  ((int)$track["duration"] / (int)$albumInfo["Album"]["duration"]) * $resolution;
            $ppf                = $this->resolutionToPositionsPerFrames((int)$track["duration"], $trackResolution);
            $curve[$track["title"]] = $this->ReviewFrames->roundReviewFramesSpan( $this->_getTrackCurveSpan($curveData, $track["id"]), $ppf, $trackResolution);
        }
        
        return array(
            "curve"         => json_encode($curve), 
            "ppf"           => $this->resolutionToPositionsPerFrames((int)$albumInfo["Album"]["duration"], $resolution),
            "score"         => $this->compileScore($curveData),
            "metacritic_score" => $this->_getMetacriticScore($this->_toMetacriticLabel($albumInfo["Album"]["name"]), $this->_toMetacriticLabel($albumInfo["Artist"]["name"]))
        );
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
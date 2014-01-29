<?php

App::uses('TableSnapshot', 'Model');
class ArtistReviewSnapshot extends TableSnapshot
{	    
	public $name        = 'ArtistReviewSnapshot';
    public $useTable    = 'artist_review_snapshots';  
    public $belongsTo   = array("Artist");     
            
    public function getCurve($artistId, $resolution = 100, $timestamp = 0)
    {
        /*
        $discographyInfo = $this->Artist->Albums->find("all", array("conditions" => array("Albums.artist_id" => $artistId), "fields" => array("Albums.duration", "Albums.name", "Albums.id")));
        $curveData = $this->getRawCurveData($timestamp);    
        $totalTime = $this->_getTotalDiscographyLength($discographyInfo);     
        $curve = array();
        $score      = $this->compileScore($curveData);      
        $split      = $this->getRawSplitData($score, $timestamp);
        $review     = new ReviewFrames();
                
        foreach($discographyInfo as $albumInfo)
        {
            $albumResolution = ceil($resolution * ((int)$albumInfo["Albums"]["duration"] / $totalTime));
            $ppf        = $this->resolutionToPositionsPerFrames((int)$albumInfo["Albums"]["duration"], $albumResolution);
            $albumSpan  = $this->_getAlbumCurveSpan($curveData, $albumInfo["Albums"]["id"]);
            $albumCurve = (new ReviewFrames())->roundReviewFramesSpan($albumSpan, $ppf, $albumResolution);
            $curve[$albumInfo["Albums"]["name"]] = $albumCurve;
        }
        
        return array(
           "curve"  => $curve, 
            "ppf"   => $this->resolutionToPositionsPerFrames($totalTime, $resolution),
            "score" => $score,
            "split" => array(
                "min" => $review->roundReviewFramesSpan($split["min"], $ppf, $resolution),
                "max" => $review->roundReviewFramesSpan($split["max"], $ppf, $resolution)
            )
        );*/
        
        $curveData  = $this->getRawCurveData($timestamp);
        $score      = $this->compileScore($curveData);      
        
        return array(
            "score" => $score
        );
    }    
    
    private function _getTotalDiscographyLength($discographyInfo)
    {
        $total = 0;
        
        foreach($discographyInfo as $album)
        {        
            $total += (int)$album["Albums"]["duration"];
        }     
        
        return $total;
    }    
    
    private function _getAlbumCurveSpan($reviewFrames, $albumId)
    {
        $startIdx = null;
        $count = 0;
        foreach($reviewFrames as $idx => $frame)
        {
            
            if(is_null($startIdx) && $frame["ReviewFrames"]["album_id"] === $albumId)
            {
                $startIdx = $idx;
            }
            
            if($startIdx >= 0 && $frame["ReviewFrames"]["album_id"] === $albumId)
            {
                $count++;
            }
        }
        
        return ($count > 0) ?
            array_slice($reviewFrames, $startIdx, $count) :
            array();
    }
    
    
}
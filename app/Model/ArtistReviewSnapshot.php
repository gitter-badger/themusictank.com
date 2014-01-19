<?php

App::uses('TableSnapshot', 'Model');
class ArtistReviewSnapshot extends TableSnapshot
{	    
	public $name        = 'ArtistReviewSnapshot';
    public $useTable    = 'artist_review_snapshots';  
    public $hasMany     = array('ReviewFrames');
    public $belongsTo   = array("Artist");     
        
    public function getCurve($artistId, $resolution = 100, $timestamp = 0)
    {
        $discographyInfo = $this->Artist->Albums->find("all", array("conditions" => array("Albums.artist_id" => $artistId)));
        $curveData = $this->getRawCurveData($timestamp);    
        $totalTime = $this->_getTotalDiscographyLength($discographyInfo);     
        $curve = array();
                
        foreach($discographyInfo as $albumInfo)
        {
            $albumResolution = ceil($resolution * ((int)$albumInfo["Albums"]["duration"] / $totalTime));
            $ppf        = $this->resolutionToPositionsPerFrames((int)$albumInfo["Albums"]["duration"], $albumResolution);
            $albumSpan  = $this->_getAlbumCurveSpan($curveData, $albumInfo["Albums"]["id"]);
            $albumCurve = $this->ReviewFrames->roundReviewFramesSpan($albumSpan, $ppf, $albumResolution);
            $curve[$albumInfo["Albums"]["name"]] = $albumCurve;
        }
        
        return array(
           "curve"  => json_encode($curve), 
            "ppf"   => $this->resolutionToPositionsPerFrames($totalTime, $resolution),
            "score" => $this->compileScore($curveData)
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
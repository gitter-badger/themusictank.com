<?php

App::uses('TableSnapshot', 'Model');
class AlbumReviewSnapshot extends TableSnapshot
{
	public $name        = 'AlbumReviewSnapshot';
    public $useTable    = 'album_review_snapshots';
    public $belongsTo   = array('Album');
    public $actsAs      = array('Metacritic');

        /*
    public function getCurve($albumId, $resolution = 100, $timestamp = 0)
    {
        $albumInfo  = $this->Album->findById($albumId);
        $curveData  = $this->getRawCurveData($timestamp);
        $score      = $this->compileScore($curveData);
        $splitData  = $this->getRawSplitData($score, $timestamp);
        $albumDuration = (int)$albumInfo["Album"]["duration"];

        $curve = array();
        $range = array("min" => array(), "max" => array());

        foreach($albumInfo["Tracks"] as $track)
        {
            $trackResolution    = ((int)$track["duration"] / $albumDuration) * $resolution;
            $ppf                = ReviewFrames::resolutionToPositionsPerFrames((int)$track["duration"], $trackResolution);

            // Handle curve
            $trackCurve         = ReviewFrames::getTrackSpan($curveData, $track["id"]);
            $curve              = array_merge($curve, ReviewFrames::lowerSpanResolution($trackCurve, $ppf, $trackResolution));

            // Handle ranges
            $trackSplitMin      = ReviewFrames::getTrackSpan($splitData["min"], $track["id"]);
            $trackSplitMax      = ReviewFrames::getTrackSpan($splitData["max"], $track["id"]);
            $range["min"]       = array_merge($range["min"], (array)ReviewFrames::lowerSpanResolution($trackSplitMin, $ppf, $trackResolution));
            $range["max"]       = array_merge($range["max"], (array)ReviewFrames::lowerSpanResolution($trackSplitMax, $ppf, $trackResolution));
        }

        return array(
            "curve" => $curve,
            "ppf"   => ReviewFrames::resolutionToPositionsPerFrames($albumDuration, $resolution),
            "score" => $score,
            "split" => $range
        );
    }

    public function getExtraSaveFields()
    {
        $saveArray  = parent::getExtraSaveFields();

        // Add the metacritic score in the save array.
        $saveArray["metacritic_score"] = $this->getMetacriticScore($this->getData("Album.name"), $this->getData("Artist.name"));

        return $saveArray;
    }*/

    public function fetch($albumId)
    {
        return $this->updateCached( array("ReviewFrames.album_id" => $albumId) );
    }
}

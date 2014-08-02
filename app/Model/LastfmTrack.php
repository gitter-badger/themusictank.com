<?php

class LastfmTrack extends AppModel
{
	public $belongsTo 	= array('Track');
    public $actsAs 		= array('Lastfm');

    public function getExpiredRange()
    {
    	return time() - WEEK;
    }

    public function updateCached()
    {
        $syncData = $this->data;
        if(count($syncData))
        {
	        if($this->requiresUpdate())
	        {
	            $trackTitle = $this->getData("Track.title");
	            $artistName = $this->getData("Artist.name");

	            $infos = $this->getLastFmTrackDetails($trackTitle, $artistName);
           		$syncData[$this->alias] = $this->_saveDetails($infos);
	        }
	    }
        return $syncData;
    }

    public function requiresUpdate()
    {
        $timestamp = (int)Hash::get($this->data, "LastfmTrack.lastsync");
        return $timestamp < $this->getExpiredRange();
    }

    private function _saveDetails($infos)
    {
        $trackId       = $this->getData("Track.id");
        $lastfmTrackId = $this->getData("LastfmTrack.id");

        if(is_null($infos))
        {
            // Save the latest updated timestamp as to not query all the time
            $this->save(array(
                "id"        => $lastfmTrackId,
                "lastsync"  => time()
            ));
            return array();
        }

        $newRow         = array(
            "id"        => $lastfmTrackId,
            "track_id"  => $trackId,
            "lastsync"  => time(),
            "wiki"      => empty($infos->wiki->content) ? null : $this->cleanLastFmWikiText($infos->wiki->content)
        );

        return $this->save($newRow);
    }
}

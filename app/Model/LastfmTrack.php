<?php

class LastfmTrack extends AppModel
{
    const CACHE_SEARCH  = "LastfmTrack-Search-%s-%d";
    const CACHE_SEARCH_TIMEOUT    = "daily";

	public $belongsTo = array('Track');
    public $actsAs = array('Lastfm');

    public function updateCached()
    {
        if($this->requiresUpdate())
        {
            $trackTitle = $this->getData("Track.title");
            $artistName = $this->getData("Artist.name");
            $infos = $this->getLastFmTrackDetails($trackTitle, $artistName);

            if($infos)
            {
                $this->_saveDetails($infos);
            }
        }
    }

    public function requiresUpdate()
    {
        $timestamp = (int)Hash::get($this->data, "LastfmTrack.lastsync");
        return $timestamp + WEEK < time();
    }
/*
    public function search($query, $limit)
    {
        $cacheName = sprintf(self::CACHE_SEARCH, $query, $limit);
   
        $result = Cache::read($cacheName, self::CACHE_SEARCH_TIMEOUT);
        if (!$result) {
            $result = $this->searchTracks($query, $limit);
            $list = $this->filterNew($result);
            if (count($list)) {
                $this->saveMany($list, array("deep" => true));
            }
            Cache::write($cacheName, $result, self::CACHE_SEARCH_TIMEOUT);
        }
    }
    

    public function filterNew($needles)
    {
        $currentList        = $this->listCurrentCollection();
        $listBeingParsed    = array();
        $returnList         = array(); 
        
        foreach($needles->track as $track)
        {               
            // only save albums of interest
            if(property_exists($track, "mbid") && trim($track->mbid) != "")
            {       
                // Add the artist to the global collection if   
                // its a new artist
                if(!array_key_exists($track->mbid, $currentList))
                {
                    // Also make sure there are no doubles inside the possible new stack
                    if(!in_array($track->mbid, $listBeingParsed))
                    {
                        $returnList[] = array(
                            "Track" => array(
                              "name" => $track->name
                            ), 
                            "LastfmTrack"  => array(
                                "mbid" => $track->mbid,
                                "artist_name" => $track->artist
                            )
                        );
                        $listBeingParsed[] = $track->mbid;
                    }
                }
            }
        }

        return $returnList;
    }  
*/

    private function _saveDetails($infos)
    {
        $trackId       = $this->getData("Track.id");
        $lastfmTrackId = $this->getData("LastfmTrack.id");

        $newRow         = array(
            "id"        => $lastfmTrackId,
            "track_id"  => $trackId,
            "lastsync"  => time(),
            "wiki"      => empty($infos->wiki->content) ? null : $this->cleanLastFmWikiText($infos->wiki->content)
        );

        return $this->save($newRow);
    }
}

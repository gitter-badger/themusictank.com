<?php

class LastfmArtist extends AppModel
{
    const CACHE_SEARCH  = "LastfmArtist-Search-%s-%d";
    const CACHE_SEARCH_TIMEOUT    = "daily";

	public $belongsTo = array('Artist');
    public $actsAs = array('Lastfm');

    public function updateCached()
    {
        if($this->requiresUpdate())
        {
            $artistName = $this->getData("Artist.name");

            $data = $this->data;
            $infos = $this->getArtistInfo($artistName);
            if($infos)
            {
                $data["LastfmArtist"] = $this->_saveDetails($infos);
            }
/*
            $ranks = $this->getArtistTopAlbums($artistName);
            if($ranks)
            {
                $this->Artist->Albums->LastfmAlbum->data = $data;
                $this->Artist->Albums->LastfmAlbum->saveNotableAlbums($ranks);
            }*/

            $this->data = $data;
        }
    }

    public function requiresUpdate()
    {
        $timestamp = (int)Hash::get($this->data, "LastfmArtist.lastsync");
        return $timestamp + WEEK < time();
    }

    public function search($query, $limit)
    {
        $cacheName = sprintf(self::CACHE_SEARCH, $query, $limit);
   
        $result = Cache::read($cacheName, self::CACHE_SEARCH_TIMEOUT);
        if (!$result) {
            $result = $this->searchArtists($query, $limit);
            $list = $this->filterNew($result);
            if (count($list)) {
                $this->Artist->saveMany($list, array('deep' => true));
            }   
            Cache::write($cacheName, $result, self::CACHE_SEARCH_TIMEOUT);
        }

        return $result;
    }
    
    public function listCurrentCollection($conditions = array())
    {
        return $this->find('list', array(
            'fields' => array('LastfmArtist.mbid', 'LastfmArtist.artist_id'),
            'conditions' => $conditions
        ));
    }

    public function filterNew($needles)
    {
        $currentList        = $this->listCurrentCollection();
        $listBeingParsed    = array();
        $returnList         = array(); 
        
        foreach($needles->artist as $artist)
        {                      
            // Add the artist to the global collection if   
            // its a new artist
            if(!array_key_exists($artist->mbid, $currentList))
            {
                // Also make sure there are no doubles inside the possible new stack
                if(!in_array($artist->mbid, $listBeingParsed))
                {
                    $returnList[] = array(
                        "LastfmArtist" => array(
                            "url"       => $artist->url,
                            "mbid" => $artist->mbid,
                            "name" => $artist->name
                        ),
                        "Artist"    => array(
                            "name" => $artist->name
                        )
                    );
                    $listBeingParsed[] = $artist->mbid;
                }
            }
        }

        return $returnList;
    }  

    private function _saveDetails($infos)
    {
        $artistId       = $this->getData("Artist.id");
        $lastfmArtistId = $this->getData("LastfmArtist.id");
        $image          = Hash::get($this->data, "LastfmArtist.image");

        $newRow         = array(
            "id"        => $lastfmArtistId,
            "artist_id" => $artistId,
            "lastsync"  => time(),
            "image"     => empty($infos->image[4]->{'#text'}) ? null : $this->getImageFromUrl($infos->image[4]->{'#text'}, $image),
            "image_src" => empty($infos->image[4]->{'#text'}) ? null : $infos->image[4]->{'#text'},
            "biography" => empty($infos->bio->summary) ? __("Biography is not available at this time.") : $this->cleanLastFmWikiText($infos->bio->content),
            "url"       => $infos->url,
            "mbid"      => $infos->mbid,
            "name"      => $infos->name
        );

        return $this->save($newRow) ? $newRow : false;
    }

}

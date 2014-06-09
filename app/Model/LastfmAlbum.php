<?php

App::uses('Track', 'Model');

class LastfmAlbum extends AppModel
{	
    const CACHE_SEARCH  = "LastfmAlbum-Search-%s-%d";
    const CACHE_SEARCH_TIMEOUT    = "daily";

	public $belongsTo   = array('Album');   
    public $actsAs      = array('Lastfm');
    
    public function updateCached()
    {
        if($this->requiresUpdate())
        {
            $artistName = $this->getData("Artist.name");
            $albumName  = $this->getData("Album.name");
            $infos      = $this->getLastFmAlbumDetails($artistName, $albumName);

            if($infos)
            {
                $this->_saveDetails($infos);
                return $infos;
            } 
        }
        return $this->data;
    }    
    
    public function requiresUpdate()
    {
        $timestamp = (int)Hash::get($this->data, "LastfmAlbum.lastsync");
        return $timestamp + WEEK < time();        
    }
/*
    public function search($query, $limit)
    {
        $cacheName = sprintf(self::CACHE_SEARCH, $query, $limit);
   
        $result = Cache::read($cacheName, self::CACHE_SEARCH_TIMEOUT);
        if (!$result) {
            $result = $this->searchAlbums($query, $limit);
            $list = $this->filterNew($result);
            if(count($list) > 0) {
                $this->saveMany($list, array("deep" => true));    
            }
            Cache::write($cacheName, $result, self::CACHE_SEARCH_TIMEOUT);
        }
    }
*/
    
    private function _saveDetails($infos)
    {
        $albumId        = $this->getData("Album.id");
        $lastfmAlbumId  = $this->getData("LastfmAlbum.id");

        $this->saveMany(array(
            "LastfmAlbum" => array(
                "id"        => $lastfmAlbumId,
                "mbid" => $infos->mbid,
                "lastsync"  => time(),
                "wiki"      => empty($infos->wiki->content) ? null : $this->cleanLastFmWikiText($infos->wiki->content)
            ),
            "Album" => array(
                "id"  => $albumId,
                "release_date" => strtotime(trim($infos->releasedate)),
                "release_date_text" => $infos->releasedate,
            )
        ), array("deep" => true)); 

        $trackData = array();
        $Track = new Track();
        $existing = $Track->listCurrentCollection($albumId);
        foreach($infos->tracks->track as $idx => $track)
        {
            //if(property_exists($track, "mbid") && trim($track->mbid) != "")
            //{
                if(!array_key_exists($track->mbid, $existing))
                {
                    $trackData[] = array(
                        "Track" => array(
                            "title" => $track->name,
                            "duration" => $track->duration,
                            "album_id" => $albumId,
                            "slug" => $Track->createSlug($track->name), 
                            "track_num" => $idx+1,
                            "LastfmTrack" => array(
                                "mbid" => $track->mbid,
                                "artist_name" => $track->artist->name
                            )
                        )
                    );
               // }
            }
        }
        
        if(count($trackData) > 0)
        {
            $Track->saveMany($trackData, array("deep" => true)); 
        }
    }    
    /*
    public function saveNotableAlbums($infos)
    {
        $titles     = array();
        $ranks      = array();
        $worked     = true;
        $artistId   = $this->getData("Artist.id");
                
        $this->Album->resetArtistNotables($artistId);
        
        foreach($infos as $topalbum)
        {
            $slug       = $this->createSlug($topalbum->name, false);
            $titles[]   = array("Album.slug LIKE" =>  $slug . "%");
            $ranks[$slug] = $topalbum->{"@attr"}->rank;
        }
        
        // Look to see if we have matching albums in our db
        $matches = $this->Album->find('list', array(
            "fields"        => array('Album.id', 'Album.slug'), 
            "conditions"    => array("or" => $titles, "Album.artist_id" => $artistId)
        ));
                
        foreach($matches as $idx => $albumslug)
        {                
            foreach($ranks as $lastfmslug => $amount)
            {
                if((int)$amount > 0 && preg_match("/^".preg_quote($albumslug)."\-?\d?/", $lastfmslug))
                {
                    $worked = $worked && $this->Album->updateAll(array("Album.notability" => 100 - (int)$amount), array("Album.id" => $idx));
                }                
            }
        }        
        return $worked;
    }*/
    
    public function listCurrentCollection()
    {
        return $this->find('list', array(
            'fields' => array('LastfmAlbum.mbid', 'LastfmAlbum.album_id')
        ));
    }
    
    public function filterNew($needles)
    {
        $currentList        = $this->listCurrentCollection();
        $listBeingParsed    = array();
        $returnList         = array(); 

        foreach($needles->album as $album)
        {                      
            // only save albums of interest
            if(property_exists($album, "mbid") && trim($album->mbid) != "")
            {
                // Add the artist to the global collection if   
                // its a new artist
                if(!array_key_exists($album->mbid, $currentList))
                {
                    // Also make sure there are no doubles inside the possible new stack
                    if(!in_array($album->mbid, $listBeingParsed))
                    {
                        $returnList[] = array(
                            "Album" => array(
                                "name" => $album->name, 
                            ),
                            "LastfmAlbum"  => array(
                                "mbid" => $album->mbid,
                                "artist_name" => $album->artist,
                                "artist_id" => null 
                            )
                        );

                        $listBeingParsed[] = $album->mbid;
                    }
                }
            }
        }
        
        return $returnList;
    }  
    
}
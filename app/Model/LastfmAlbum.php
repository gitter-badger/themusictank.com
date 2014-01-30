<?php

class LastfmAlbum extends AppModel
{	
	public $belongsTo = array('Album');   
    public $actsAs = array('Lastfm');
    
    public function updateCached()
    {
        if($this->requiresUpdate())
        {
            $infos = $this->getLastFmAlbumDetails($this->data["Artist"]["name"], $this->data["Album"]["name"]);
            if($infos)
            {
                $this->_saveDetails($infos) !== false;
            } 
        }
    }    
    
    public function requiresUpdate()
    {
        return $this->data["LastfmAlbum"]["lastsync"] + 60*60*24*5 < time();        
    }
    
    private function _saveDetails($infos)
    {
        $albumId       = $this->data["Album"]["id"];
        $lastfmAlbumId = $this->data["LastfmAlbum"]["id"];     
        
        $newRow         = array(
            "id" => $lastfmAlbumId,
            "album_id" => $albumId,
            "lastsync"  => time(),
            "wiki" => empty($infos->wiki->content) ? null : $this->cleanLastFmWikiText($infos->wiki->content)
        );
            
        return $this->save($newRow);            
    }    
    
    public function saveNotableAlbums($infos)
    {
        $titles = array();
        $ranks = array();
        $worked = true;
        $artistId = $this->data["Artist"]["id"];
                
        $this->Album->resetArtistNotables($artistId);
        
        foreach($infos as $topalbum)
        {
            $slug       = $this->createSlug($topalbum->name, false);
            $titles[]   = array("Album.slug LIKE" =>  $slug . "%");
            $ranks[$slug] = $topalbum->{"@attr"}->rank;
        }
        
        // Look to see if we have matching albums in our db
        $matches = $this->Album->find('list', array(
            "fields" => array('Album.id', 'Album.slug'), 
            "conditions" => array("or" => $titles, "Album.artist_id" => $artistId)
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
    }
    
    
}
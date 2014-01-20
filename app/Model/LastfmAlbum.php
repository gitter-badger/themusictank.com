<?php

class LastfmAlbum extends AppModel
{	
	public $belongsTo = array('Album');   
    
    
    public function requiresUpdate($data = null)
    {
        if(!is_null($data)) $this->data = $data;
        return $this->data["LastfmAlbum"]["lastsync"] + 60*60*24*5 < time();        
    }
    
    public function saveDetails($data, $infos)
    {
        $albumId       = $data["Album"]["id"];
        $lastfmAlbumId = $data["LastfmAlbum"]["id"];     
        
        $newRow         = array(
            "id" => $lastfmAlbumId,
            "album_id" => $albumId,
            "lastsync"  => time(),
            "wiki" => empty($infos->wiki->content) ? null : $this->_cleanWikiText($infos->wiki->content)
        );
            
        return $this->save($newRow);            
    }
    
    
    public function saveNotableAlbums($data, $infos)
    {
        $titles = array();
        $ranks = array();
        $worked = true;
                
        $this->Album->resetArtistNotables($data["Artist"]["id"]);
        
        foreach($infos as $topalbum)
        {
            $slug       = $this->createSlug($topalbum->name, false);
            $titles[]   = array("Album.slug LIKE" =>  $slug . "%");
            $ranks[$slug] = $topalbum->{"@attr"}->rank;
        }
        
        // Look to see if we have matching albums in our db
        $matches = $this->Album->find('list', array(
            "fields" => array('Album.id', 'Album.slug'), 
            "conditions" => array("or" => $titles, "Album.artist_id" => $data["Artist"]["id"])
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
    
    private function _cleanWikiText($text)
    {
        return trim(strip_tags(preg_replace('/Read more about .* on .*/', '', $text)));
    }
    
}
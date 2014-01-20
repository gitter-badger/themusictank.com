<?php

class RdioAlbum extends AppModel
{	
	public $belongsTo = array('Album');    
       
    public function requiresUpdate($data = null)
    {           
        if(!is_null($data)) $this->data = $data;
        return !((int)$this->data["RdioAlbum"]["lastsync"] > 1);
    }
    
    // Save the last sync timestamp
    public function setSyncTimestamp($rdioAlbumData)
    {        
        $this->id = $rdioAlbumData["RdioAlbum"]["id"];
        return $this->saveField("lastsync", time());
    }
        
    public function listCurrentCollection($artistkey)
    {
        return $this->find("list", array(
            "conditions"    => array("artist_key" => $artistkey),
            "fields"        => array('RdioAlbum.key', 'RdioAlbum.album_id')
        ));
    }       
    
    public function getListByKeys($rdioKeys)
    {
        return $this->find('list', array('fields' => array('key', 'album_id'), "conditions" => array("RdioAlbum.key" => $rdioKeys)));
    }
    
    public function filterNew($artistid, $artistkey, $needles)
    {        
        $currentList = $this->listCurrentCollection($artistkey);        
        $returnList = array();
                
        foreach($needles as $album)
        {            
            // Add the artist to the global collection if
            // its a new artist
            if(!array_key_exists($album->key, $currentList))
            {                
                $returnList[] = array(
                    "name" => $album->name,   
                    "image_src" => $album->icon,
                    "image"     => $this->getImageFromUrl($album->icon),
                    "release_date_text" => $album->releaseDate,
                    "release_date" => strtotime($album->releaseDate),
                    "artist_id" => $artistid,
                    "duration" => $album->duration,
                    "RdioAlbum"  => array(   
                        "key" => $album->key,
                        "artist_key" => $album->artistKey,
                        "url" => $album->url
                    )
                );
            }
        }
        
        return $returnList;
    }
}
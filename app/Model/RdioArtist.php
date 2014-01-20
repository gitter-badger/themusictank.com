<?php

class RdioArtist extends AppModel
{	
	public $belongsTo = array('Artist');    
    
    public function requiresUpdate($data = null)
    {   
        if(!is_null($data)) $this->data = $data;
        return $this->data["RdioArtist"]["lastsync"] + 60*60*24*5 < time();
    }    
    
    public function listCurrentCollection()
    {
        return $this->find('list', array(
            'fields' => array('RdioArtist.key', 'RdioArtist.artist_id')
        ));
    }
    
    public function filterNew($needles)
    {
        $currentList = $this->listCurrentCollection();
        $listBeingParsed = array();
        $returnList = array();
        
        foreach($needles as $artist)
        {
            if(property_exists($artist, "artistKey"))
            {
                $artistKey = "artistKey";
            }
            else
            {
                $artistKey = "key";
            }
            
            if(property_exists($artist, "artist"))
            {
                $artistNameKey = "artist";                
            }
            else
            {
                $artistNameKey = "name";                
            }
                                    
            // Add the artist to the global collection if   
            // its a new artist
            if(!array_key_exists($artist->{$artistKey}, $currentList))
            {
                // Also make sure there are no doubles inside the possible new stack
                if(!in_array($artist->{$artistKey}, $listBeingParsed))
                {
                    $returnList[] = array(
                        "name" => $artist->{$artistNameKey}, 
                        "RdioArtist"  => array(
                            "key" => $artist->{$artistKey}
                        )
                    );
                    $listBeingParsed[] = $artist->{$artistKey};
                }
            }
        }
        
        return $returnList;
    }    
    
    public function makePopular($popularArtists)
    {   
        $keys = array();
        foreach($popularArtists as $artist)
        {
            $keys[] = $artist->key;
        }
        
        return $this->updateAll(array("is_popular" => true), array("key" => $keys)); 
    }
    
    public function resetPopular()
    { 
        return $this->updateAll(array("is_popular" => false), array("is_popular" => true));
    }    
    
    public function getListByKeys($rdioKeys)
    {
        return $this->find('list', array('fields' => array('key', 'artist_id'), "conditions" => array("RdioArtist.key" => $rdioKeys)));
    }
    
    // Save the last sync timestamp
    public function setSyncTimestamp($rdioUserData)
    {
        $this->id = $rdioUserData["RdioArtist"]["id"];
        return $this->saveField("lastsync", time());
    }
}


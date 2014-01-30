<?php

class LastfmArtist extends AppModel
{	
	public $belongsTo = array('Artist');    
    public $actsAs = array('Lastfm'); 
            
    public function updateCached()
    {
        if($this->requiresUpdate())
        {   
            $data = $this->data;
            $infos = $this->getArtistBiography($data["Artist"]["name"]);
            if($infos)
            {
                $this->_saveDetails($infos);
            } 
            
            $ranks = $this->getArtistTopAlbums($data["Artist"]["name"]);
            if($ranks)
            {
                $this->Artist->Albums->LastfmAlbum->data = $data;
                $this->Artist->Albums->LastfmAlbum->saveNotableAlbums($ranks);
            } 
        }
    }    
    
    public function requiresUpdate()
    {
        return $this->data["LastfmArtist"]["lastsync"] + 60*60*24*5 < time();        
    }
    
    private function _saveDetails($infos)
    {
        $artistId       = $this->data["Artist"]["id"];
        $lastfmArtistId = $this->data["LastfmArtist"]["id"];     
        
        $newRow         = array(
            "id" => $lastfmArtistId,
            "artist_id" => $artistId,
            "lastsync"  => time(),
            "image"     => empty($infos->image[3]->{'#text'}) ? null : $this->getImageFromUrl($infos->image[3]->{'#text'}, $this->data["LastfmArtist"]["image"]),
            "image_src" => empty($infos->image[3]->{'#text'}) ? null : $infos->image[3]->{'#text'},
            "biography" => empty($infos->bio->summary) ? __("Biography is not available at this time.") : $this->cleanLastFmWikiText($infos->bio->summary),
            "url"       => $infos->url
        );
            
        return $this->save($newRow);            
    }    
    
}
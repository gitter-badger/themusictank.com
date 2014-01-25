<?php

class LastfmArtist extends AppModel
{	
	public $belongsTo = array('Artist');   
    
    
    public function requiresUpdate($data = null)
    {
        if(!is_null($data)) $this->data = $data;
        return $this->data["LastfmArtist"]["lastsync"] + 60*60*24*5 < time();        
    }
    
    public function saveDetails($data, $infos)
    {
        $artistId       = $data["Artist"]["id"];
        $lastfmArtistId = $data["LastfmArtist"]["id"];     
        debug($data);
        $newRow         = array(
            "id" => $lastfmArtistId,
            "artist_id" => $artistId,
            "lastsync"  => time(),
            "image"     => empty($infos->image[3]->{'#text'}) ? null : $this->getImageFromUrl($infos->image[3]->{'#text'}, $data["LastfmArtist"]["image"]),
            "image_src" => empty($infos->image[3]->{'#text'}) ? null : $infos->image[3]->{'#text'},
            "biography" => empty($infos->bio->summary) ? __("Biography is not available at this time.") : $this->_cleanBioText($infos->bio->summary),
            "url"       => $infos->url
        );
            
        return $this->save($newRow);            
    }    
    
    private function _cleanBioText($text)
    {
        return trim(strip_tags(preg_replace('/Read more about .* on .*/', '', $text)));
    }
    
}
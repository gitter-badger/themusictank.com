<?php

class LastfmTrack extends AppModel
{	
	public $belongsTo = array('Track');   
        
    public function requiresUpdate($data = null)
    {
        if(!is_null($data)) $this->data = $data;
        return $this->data["LastfmTrack"]["lastsync"] + 60*60*24*5 < time();        
    }
    
    public function saveDetails($data, $infos)
    {
        $trackId       = $data["Track"]["id"];
        $lastfmTrackId = $data["LastfmTrack"]["id"];     
        
        $newRow         = array(
            "id" => $lastfmTrackId,
            "track_id" => $trackId,
            "lastsync"  => time(),
            "wiki" => empty($infos->wiki->content) ? null : $this->_cleanWikiText($infos->wiki->content)
        );
            
        return $this->save($newRow);            
    }
    
    private function _cleanWikiText($text)
    {
        return trim(strip_tags(preg_replace('/Read more about .* on .*/', '', $text)));
    }
    
}
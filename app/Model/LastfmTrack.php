<?php

class LastfmTrack extends AppModel
{	
	public $belongsTo = array('Track');  
    public $actsAs = array('Lastfm'); 
    
    public function updateCached()
    {
        if($this->requiresUpdate())
        {
            $infos = $this->getLastFmTrackDetails($this->data["Track"]["title"], $this->data["Artist"]["name"]);
            if($infos)
            {
                $this->_saveDetails($infos) !== false;
            } 
        }
    }        
    
    public function requiresUpdate()
    {
        return $this->data["LastfmTrack"]["lastsync"] + 60*60*24*5 < time();        
    }
    
    private function _saveDetails($infos)
    {
        $trackId       = $this->data["Track"]["id"];
        $lastfmTrackId = $this->data["LastfmTrack"]["id"];     
        
        $newRow         = array(
            "id" => $lastfmTrackId,
            "track_id" => $trackId,
            "lastsync"  => time(),
            "wiki" => empty($infos->wiki->content) ? null : $this->cleanLastFmWikiText($infos->wiki->content)
        );
            
        return $this->save($newRow);            
    }    
}
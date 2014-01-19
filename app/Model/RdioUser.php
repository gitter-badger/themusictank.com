<?php

class RdioUser extends AppModel
{	
	public $belongsTo = array('User');    
    
    public function requiresUpdate($data = null)
    {   
        if(!is_null($data)) $this->data = $data;
        return $this->data["RdioUser"]["lastsync"] + 60*60*24 < time();
    } 
    
    // Save the last sync timestamp
    public function setSyncTimestamp($rdioUserData)
    {
        $this->User->RdioUser->id = $rdioUserData["RdioUser"]["id"];
        return $this->User->RdioUser->saveField("lastsync", time());
    }
    
}
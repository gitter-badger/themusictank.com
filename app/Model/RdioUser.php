<?php

class RdioUser extends AppModel
{	
	public $belongsTo = array('User');    
    
    public function requiresUpdate($data = null)
    {   
        if(!is_null($data)) $this->data = $data;
        return (int)$this->data["RdioUser"]["lastsync"] + 60*60*24 < time();
    } 
    
    // Save the last sync timestamp
    public function setSyncTimestamp($data = null)
    {
        if($data["RdioUser"]["id"])
        {
            $this->id = $data["RdioUser"]["id"];
            return $this->saveField("lastsync", time());
        }
        
        return false;
    }
        
    public function getFromUserId($userId)
    {        
        return $this->find("first", array(
            "conditions"    => array("RdioUser.user_id" => $userId),
            "fields"        => array("RdioUser.id", "RdioUser.lastsync")
        ));        
    }
    
    public function createFromAPI($userInfo, $mergedUserData = null)
    {        
        // Create a new user from the Rdio profile
        $formattedData = array(
            "image_src"      => $userInfo->icon,
            "image"         => $this->User->getImageFromUrl($userInfo->icon),
            "prefered_player_api" => 1,
            "RdioUser"  => array("key" => $userInfo->key)
        );
        
        if(isset($mergedUserData))
        {            
            $formattedData["id"]    = $mergedUserData["User"]["id"];
            $formattedData["slug"]  = $mergedUserData["User"]["slug"];
        }
        else
        {   
            $formattedData["firstname"] = $userInfo->firstName;
            $formattedData["lastname"]  = $userInfo->lastName;
            $this->User->create();
        }
                
        if($this->User->saveAll($formattedData))    
        {   
            return $this->User->read(null, $this->User->id);
        }
    }
    
}
<?php

App::uses('Artist', 'Model');
App::uses('UserActivity', 'Model');
App::uses('CakeSession', 'Model/Datasource');

class RdioUser extends AppModel
{	
	public $belongsTo   = array('User');    
    public $actsAs       = array('Rdio');
    
    public function updateCached()
    {
        if($this->requiresUpdate())
        {    
            $userKey = $this->getData("RdioUser.key");
            $artists = $this->getRdioArtistLibrary($userKey);
            if($artists)
            {                   
                $artist = new Artist();
                $filtered = $artist->RdioArtist->filterNew($artists);
                
                if(count($filtered) > 0)
                {
                    $artist->data = $this->data;                
                    $artist->saveMany($filtered, array('deep' => true));                                    
                    
                    $this->User->reward((int)CakeSession::read('Auth.User.User.id'), UserActivity::TYPE_CREATED_ARTIST);
                }
                
                $this->setSyncTimestamp();
            }
        }
    }    
    
    public function requiresUpdate()
    {   
        $timestamp = (int)$this->getData("RdioUser.lastsync");
        return $timestamp + DAY < time();
    } 
    
    // Save the last sync timestamp
    public function setSyncTimestamp()
    {
        $this->id = $this->getData("RdioUser.id");
        return $this->saveField("lastsync", time());
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
            "preferred_player_api" => 1,
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
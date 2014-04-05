<?php

class FacebookUser extends AppModel
{	
	public $belongsTo = array('User');    
    
    public function createFromAPI($userInfo, $mergedUserData = null)
    {
        // Create a new user from the Rdio profile
        $formattedData = array(
            "FacebookUser"  => array("facebook_id" => $userInfo->id)
        );
        
        if(isset($mergedUserData))
        {            
            $formattedData["id"]    = $mergedUserData["User"]["id"];
            $formattedData["slug"]  = $mergedUserData["User"]["slug"];
        }
        else
        {   
            $formattedData["firstname"] = $userInfo->first_name;
            $formattedData["lastname"]  = $userInfo->last_name;
            $this->User->create();
        }
                
        if($this->User->saveAll($formattedData))    
        {   
            return $this->User->read(null, $this->User->id);
        }    
    }
    
}
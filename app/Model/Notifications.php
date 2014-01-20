<?php

class Notifications extends AppModel
{	
    public $belongTo    = array("User");      
    
    public function markAsRead($timestamp)
    {
        return $this->updateAll(array("is_viewed" => true), array("created < " => $timestamp));  
    }
    
    public function findByUserId($userId, $limit = 10)
    {   
        return $this->find("all", array(
            "conditions" => array("user_id" => $userId),
            "limit" => $limit
        ));
    }
    
    public function afterFind($results, $primary = false)
    {   
        foreach($results as $idx => $notice)
        {
            if(!empty($notice["Notifications"]["related_model_id"]))
            {
                switch($notice["Notifications"]["type"])
                {
                    case "achievement" : 
                        $results[$idx]["Notifications"]["Achievement"] = $this->_loadLinkedAchievement($notice["Notifications"]["related_model_id"]);
                        break;
                }
            }
        }
        
        return $results;
    }        
    
    private function _loadLinkedAchievement($achievementId)
    {
        $achievement = ClassRegistry::init('Achievement')->getById($achievementId);
        return $achievement["Achievement"];
    }
    
} 
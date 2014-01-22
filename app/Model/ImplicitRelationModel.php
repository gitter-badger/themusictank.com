<?php

class ImplicitRelationModel extends AppModel
{	        
    private $_preloadedObjects = array(
        "Achievement" => array(),
        "User" => array()
    );
        
    public function associateRelated($results)
    {           
        foreach($results as $idx => $row)
        {
            if(!empty($row[$this->alias]["related_model_id"]))
            {
                switch(strtolower($row[$this->alias]["type"]))
                {
                    case "achievement" : 
                        if(!array_key_exists("Achievement", $results[$idx][$this->alias]))
                        {
                            $results[$idx][$this->alias]["Achievement"] = $this->_loadLinkedAchievement((int)$row[$this->alias]["related_model_id"]);
                        }
                        break;

                    case "follower" :
                        if(!array_key_exists("UserFollower", $results[$idx][$this->alias]))
                        {
                            $results[$idx][$this->alias]["UserFollower"] = $this->_loadLinkedUser((int)$row[$this->alias]["related_model_id"]);
                        }
                        break;                        
                }
            }
        }
        
        return $results;
    }        
    
    private function _isCached($type, $key)
    {
        $key = "_" . $key;
        return array_key_exists($key, $this->_preloadedObjects[$type]) && !is_null($this->_preloadedObjects[$type][$key]);
    }
    
    private function _getCached($type, $key)
    {
        $key = "_" . $key;
        return $this->_preloadedObjects[$type][$key];
    }
    
    private function _saveToCache($type, $key, $obj)
    {
        $key = "_" . $key;
        $this->_preloadedObjects[$type][$key] = $obj;
    }
    
    private function _loadLinkedAchievement($achievementId)
    {
        if(!$this->_isCached("Achievement", $achievementId))
        {
            $achievement = ClassRegistry::init('Achievement')->getById($achievementId);
            $this->_saveToCache("Achievement", $achievementId, $achievement["Achievement"]);
        }
        
        return $this->_getCached("Achievement", $achievementId);
    }
    
    private function _loadLinkedUser($userId)
    {
        if(!$this->_isCached("User", $userId))
        {
            $user = ClassRegistry::init('User')->find("first", array(
                "conditions" => array("User.id" => $userId),
                "fields" => "User.*"
            ));
            
            $this->_saveToCache("User", $userId, $user["User"]);
        }
        return $this->_getCached("User", $userId);
    }
    
} 
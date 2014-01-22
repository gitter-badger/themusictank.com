<?php

class UserAchievements extends AppModel
{	    
    public $belongsTo   = array("Achievement");
    
    public function checkIfApplies($userId, $achievementId, $value = null)
    {        
        $conditions = array(
            "UserAchievements.user_id"          => $userId,
            "UserAchievements.achievement_id"   => $achievementId
        );
        
        return $this->find("count", array("conditions" => $conditions)) < 1;
    }    
    
    public function grant($userId, $achievementId)
    {
        return $this->save(array("user_id" => $userId, "achievement_id" => $achievementId));        
    }
    
    public function getAchievementDetails($id)
    {
        return $this->Achievement->findById($id);
    }    
    
    
    public function afterSave($created, $options = array())
    {
        if($created)
        {
            $this->dispatchEvent('onCreate');
        }
    }
    
}
<?php
App::uses('UserActivity', 'Model');

class UserAchievements extends AppModel
{	    
    public $belongsTo   = array("Achievement");
    
    public function notObtained($userId, $achievementId)
    {        
        $conditions = array(
            "UserAchievements.user_id"          => $userId,
            "UserAchievements.achievement_id"   => $achievementId
        );
        
        return $this->find("count", array("conditions" => $conditions)) < 1;
    }    
    
    public function grant($userId, $achievementId)
    {
        $this->create();
        if($this->save(array("user_id" => $userId, "achievement_id" => $achievementId)))
        {
            $activity = new UserActivity();
            return $activity->add($userId, UserActivity::TYPE_ACHIEVEMENT, $achievementId);
        }
        
        return false;
    }
    
    public function getAchievementDetails($id)
    {
        return $this->Achievement->findById($id);
    }    
}
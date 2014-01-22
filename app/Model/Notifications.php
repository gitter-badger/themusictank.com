<?php

App::uses('ImplicitRelationModel', 'Model');
class Notifications extends ImplicitRelationModel
{	    
    public $belongsTo    = array("User");
    
    public function markAsRead($timestamp)
    {
        return $this->updateAll(array("is_viewed" => true), array("created < " => $timestamp));  
    }
    
    public function findByUserId($userId, $limit = 10)
    {    
        return $this->find("all", array(
                "conditions" => array("user_id" => $userId),
                "limit" => $limit
            )
        );
    }
    
} 
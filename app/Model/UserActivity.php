<?php
App::uses('ImplicitRelationModel', 'Model');
class UserActivity extends ImplicitRelationModel
{	            
    public $belongsTo    = array("User");
    
    public function add($userId, $activityType, $relatedModelId)
    {
         return $this->save(array("user_id" => $userId, "type" => $activityType, "related_model_id" => $relatedModelId));  
    }
 
    public function fetchActivity($idList)
    {        
        return $this->associateRelated(
                $this->find("all", array(
                    "conditions"    => array("user_id" => $idList),
                    "limit"         => 20,
                    "order"         => array("UserActivity.created DESC")
                )
            )
        );
    }   
    
}
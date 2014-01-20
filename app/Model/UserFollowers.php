<?php

class UserFollowers extends AppModel
{	
    public $belongsTo = array('User');
    
    public function addRelation($userId, $followerSlug)
    {
        $follower = $this->User->find("first", array(
            "conditions"    => array("User.slug" => $followerSlug),
            "fields"        => array("User.id")
        ));
     
        if(!$this->relationExists($follower["User"]["id"], $userId))
        {        
            return $this->save(array(
                "user_id"       => $userId, 
                "follower_id"   => $follower["User"]["id"]
            ));
        }
        return true;
    }
    
    public function removeRelation($userId, $followerSlug)
    {
        $follower = $this->User->find("first", array(
            "conditions"    => array("User.slug" => $followerSlug),
            "fields"        => array("User.id")
        ));
        
        return $this->deleteAll(array(
            "user_id"       => $userId, 
            "follower_id"   => $follower["User"]["id"]
        ));
    }
    
    public function relationExists($followerId, $userId)
    {        
        if($followerId == $userId)
        {
            return true;
        }        
        
        return $this->find('count', array(
            'conditions' => array(
                'user_id'       => $userId, 
                'follower_id'   => $followerId
            )
        )) > 0;
    }
    
    public function getFollowers($userId)
    {
        return $this->find('all', array('conditions' => array('follower_id'  => $userId), 'fields' => array("User.*")));
    }
    
    public function getFollowing($userId)
    {
        return $this->find('all', array('conditions' => array('user_id'  => $userId), 'fields' => array("User.*")));
    }
}
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

    /*  User_id = the guy that is clicking on the follow button
        Follower_id = the person that user_id is subscribing to. */
    public function getFollowers($userId)
    {
        return $this->find("list", array('conditions' => array('follower_id'  => $userId), "fields" => "UserFollowers.user_id"));
    }

    public function getSubscriptions($userId)
    {        
        return $this->find("list", array('conditions' => array('user_id'  => $userId), "fields" => "UserFollowers.follower_id"));
    }
    
}
<?php
App::uses('UserActivity', 'Model');

class UserFollowers extends AppModel
{	
    public $belongsTo = array('User');
    
    public function addRelation($userId, $followerSlug)
    {
        $follower = $this->User->find("first", array(
            "conditions"    => array("User.slug" => $followerSlug),
            "fields"        => array("User.id", "User.firstname", "User.lastname")
        ));
     
        if(!$this->relationExists($follower["User"]["id"], $userId))
        {                    
            if($this->save(array("user_id" => $userId, "follower_id" => $follower["User"]["id"])))
            {
                $activity = new UserActivity();
                $activity->add($userId, UserActivity::TYPE_FOLLOWER, $follower["User"]["id"]);
                
                $msg = $follower["User"]["firstname"] . " " . $follower["User"]["lastname"] . " " .__("is now following you.");
                $this->User->notify($follower["User"]["id"], UserActivity::TYPE_FOLLOWER, $msg, $follower["User"]["id"]);
                
                return true;
            }            
            return false;
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
    
    /**
     * Returns a list of people following $followerId
     * @param type $followerId
     * @return type
     */
    public function getFollowers($followerId, $flat = false)
    {
        $data = $this->find("list", array('conditions' => array('follower_id'  => $followerId), "fields" => "UserFollowers.user_id"));
        if(!$flat) return $data;
        return array_values($data);
    }
    /**
     * Returns a list of people $userId subscribed to
     * @param type $followerId
     * @return type
     */
    public function getSubscriptions($userId, $flat = false)
    {        
        $data = $this->find("list", array('conditions' => array('user_id'  => $userId), "fields" => "UserFollowers.follower_id"));
        if(!$flat) return $data;
        return array_values($data);
    }    
}
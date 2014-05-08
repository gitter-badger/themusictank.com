<?php

class ProfilesController extends AppController {
    
    var $helpers    = array("Chart");
    var $components = array("Paginator");
    var $paginate = array(
        'limit' => 25,
        'order' => array(
            'Notifications.created' => 'desc'
        )
    );

	/** 
     * Read only view of a user's details
     */
    public function view($userSlug = null)
    {
        $data = $this->_getUserFromSlug($userSlug);
                
        if(!$data)
        {
            throw new NotFoundException('Could not find that user');
        }

        $this->loadModel("TrackReviewSnapshot");          
        $recentReviews = $this->TrackReviewSnapshot->getRecentReviews(array("user_id" => $data["User"]["id"]));
        foreach($recentReviews as $idx => $review)
        {            
            $recentReviews[$idx]["appreciation"] = $this->TrackReviewSnapshot->getUserAppreciation($review["Track"]["id"], $data["User"]["id"]);
        }
            
        $relationExists = false;
        if($this->userIsLoggedIn())
        {
            $relationExists = $this->User->UserFollowers->relationExists($data["User"]["id"], $this->getAuthUserId());
        }        
        $data["User"]["currently_followed"] = $relationExists;
                
        $this->set("user",          $data['User']);                
        $this->set("recentReviews", $recentReviews);
        
        $this->setPageTitle(array(__("Profile"), User::getFullName($data['User'])));  
        $this->setPageMeta(array(
            "keywords" => array(__("user profile"), __("review summary")),
            "description" => sprintf(__("%s's profile page on The Music Tank contains all the recent activity on The Music Tank."), User::getFullName($data['User'])), 
        ));
    }

	public function followers($userSlug = null)
    {       
        $data = $this->_getUserFromSlug($userSlug); 
        
        $followers = $this->_addSessionFollowStatus(
            $this->User->getFollowers($data["User"]["id"]),
            $data["User"]["id"]
        );
        
        $relationExists = false;
        if($this->userIsLoggedIn())
        {
            $relationExists = $this->User->UserFollowers->relationExists($data["User"]["id"], $this->getAuthUserId());
        }        
        $data["User"]["currently_followed"] = $relationExists;

        $this->set("followers", $followers);
        $this->set("user", $data['User']);         
        
        $this->setPageTitle(array(__("Followers"), User::getFullName($data['User'])));  
        $this->setPageMeta(array(
            "keywords" => array(__("user followers")),
            "description" => sprintf(__("%s's followers on The Music Tank."), User::getFullName($data['User'])), 
        ));
    }
    
    public function subscriptions($userSlug = null)
    {       
        $data = $this->_getUserFromSlug($userSlug);

        $subscriptions = $this->_addSessionFollowStatus(
            $this->User->getSubscribers($data["User"]["id"]),
            $data["User"]["id"]
        );

        $relationExists = false;
        if($this->userIsLoggedIn())
        {
            $relationExists = $this->User->UserFollowers->relationExists($data["User"]["id"], $this->getAuthUserId());
        }        
        $data["User"]["currently_followed"] = $relationExists;

        $this->set("subscriptions", $subscriptions);
        $this->set("user", $data['User']);
        
        $this->setPageTitle(array(__("Subscriptions"), User::getFullName($data['User']))); 
        $this->setPageMeta(array(
            "keywords" => array(__("user subscriptions")),
            "description" => sprintf(__("%s's subscriptions on The Music Tank."), User::getFullName($data['User'])), 
        ));
    }

 	/** 
     * Lists the complete details of all user notifications. 
     */
    public function achievements($userSlug = null)
    {		
        $data = $this->_getUserFromSlug($userSlug);
        

        $relationExists = false;
        if($this->userIsLoggedIn())
        {
            $relationExists = $this->User->UserFollowers->relationExists($data["User"]["id"], $this->getAuthUserId());
        }        
        $data["User"]["currently_followed"] = $relationExists;
        
        $this->set('achievements', $this->User->UserAchievements->findAllByUserId($data["User"]["id"], null, "created DESC"));    
        $this->set("user", $data['User']);         
        
        $this->setPageTitle(array(__("Unlocked achievements"), User::getFullName($data['User'])));
        $this->setPageMeta(array(
            "keywords" => array(__("user achievements")),
            "description" => sprintf(__("%s's unlocked achievements on The Music Tank."), User::getFullName($data['User'])), 
        ));
    }

    public function _addSessionFollowStatus($subscriptions, $profileId)
    {
        if($this->userIsLoggedIn())
        {
            if($this->getAuthUserId() != $profileId)
            {
                $sessionSubscriptions = array_values($this->User->UserFollowers->getSubscriptions($this->getAuthUserId()));
                foreach($subscriptions as $idx => $user)
                {
                    $subscriptions[$idx]["User"]["currently_followed"] = in_array($user["User"]["id"], $sessionSubscriptions);
                }
            }
            else
            {
                foreach($subscriptions as $idx => $user)
                {
                    $subscriptions[$idx]["User"]["currently_followed"] = true;
                }
            }
        }
        return $subscriptions;
    }

    private function _getUserFromSlug($userSlug)
    {
        $data = $this->User->findBySlug($userSlug);
        if(!$data)
        {
            throw new NotFoundException('Could not find that user');
        }    
        return $data;        
    }
    
}
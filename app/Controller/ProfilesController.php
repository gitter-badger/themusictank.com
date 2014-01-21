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
        $data = $this->User->findBySlug($userSlug);
        if(!$data)
        {
            $this->Session->setFlash(__('This user does not exist.'), 'Flash'.DS.'failure');
            $this->redirect(array('controller' => 'pages', 'action' => 'error'));
        }
                
        $this->loadModel("TrackReviewSnapshot");  
        $topAreas       = $this->TrackReviewSnapshot->getTopAreasByUserId($data["User"]["id"]);
        $recentReviews  = $this->TrackReviewSnapshot->getRecentReviewsByUserId($data["User"]["id"], 5);
                        
        foreach($recentReviews as $idx => $review)
        {
            $recentReviews[$idx]["appreciation"] = $this->TrackReviewSnapshot->getUserAppreciation($review["Track"]["id"], $data["User"]["id"]);
        }
            
        $relationExists = false;
        if($this->userIsLoggedIn())
        {
            $relationExists = $this->User->UserFollowers->relationExists($data["User"]["id"], $this->getAuthUserId());
        }        
        
        $this->set("user",          $data['User']);                
        $this->set("recentReviews", $recentReviews);
        $this->set("topAreas",      $topAreas);
        $this->set("relationExists", $relationExists);
        
        $this->setPageTitle(array($data["User"]["firstname"]));
    }    

	public function followers($userSlug = null)
    {
        $data = $this->User->findBySlug($userSlug);
        if(!$data)
        {
            $this->Session->setFlash(__('This user does not exist.'), 'Flash'.DS.'failure');
            $this->redirect(array('controller' => 'pages', 'action' => 'error'));
        }        
        
        $this->set("user", $data['User']);            
        $followers = $this->User->getFollowers($data["User"]["id"]);
        $followers = $this->_addSessionFollowStatus($followers, $data["User"]["id"]);
        $this->set("followers", $followers);        
    }
    
    public function subscriptions($userSlug = null)
    {
        $data = $this->User->findBySlug($userSlug);
        if(!$data)
        {
            $this->Session->setFlash(__('This user does not exist.'), 'Flash'.DS.'failure');
            $this->redirect(array('controller' => 'pages', 'action' => 'error'));
        }

        $this->set("user", $data['User']);
        $subscriptions = $this->User->getSubscribers($data["User"]["id"]);
        $subscriptions = $this->_addSessionFollowStatus($subscriptions, $data["User"]["id"]);

        $this->set("subscriptions", $subscriptions);
    }

 	/** 
     * Lists the complete details of all user notifications. 
     */
    public function achievements($userSlug = null)
    {
		$data = $this->User->findBySlug($userSlug);
        if(!$data)
        {
            $this->Session->setFlash(__('This user does not exist.'), 'Flash'.DS.'failure');
            $this->redirect(array('controller' => 'pages', 'action' => 'error'));
        }  

        $achievements = $this->User->UserAchievements->findAllByUserId($data["User"]["id"]);
        $this->set('achievements', $achievements);    
        $this->set("user", $data['User']);         
        $this->setPageTitle(array(__("Unlocked achievements")));
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

}
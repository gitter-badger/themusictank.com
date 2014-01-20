<?php
/**
 * UsersController controller
 *
 * Contains Users pages methods
 *
 * @package       app.Controller
 */
class UsersController extends AppController {
    
    var $helpers    = array("Chart");
    var $components = array("Paginator");
    var $paginate = array(
        'limit' => 25,
        'order' => array(
            'Notifications.created' => 'desc'
        )
    );
             
    
    public function wheredoesthiscommit() {
        return "nowhere bro";
    }
    public function wheredoesthiscommi2t() {
        return "nowhere bro";
    }
    
    public function beforeFilter()
    {   
        parent::beforeFilter();   
        $this->Auth->deny("edit", "dashboard", "notifications", "whatsup", "okstfu");
    }
    
    /** 
     * Json call that lists recent Notifications for the current user
     */
    public function whatsup()
    {
        $this->layout = "ajax";        
        $this->set("notifications", $this->User->Notifications->findByUserId($this->Session->read('Auth.User.User.id'), 5));                
        $this->render('/Ajax/dropdownnotifications/');
    }
    
    /** 
     * Json call that changes the status of notifications to 'read'
     */
    public function okstfu()
    {          
        $this->User->Notifications->markAsRead(time());   
        $this->redirect(array('controller' => 'users', 'action' => 'whatsup'));
    }
        
    public function follow($userSlug)
    {
        $this->layout   = "ajax";        
        $relationExists = false;
        
        if($this->userIsLoggedIn())
        {
            $sessionId = $this->Session->read('Auth.User.User.id');
            $relationExists = $this->User->UserFollowers->addRelation($sessionId, $userSlug);
        }  
        
        $this->set("user", array("slug" => $userSlug)); 
        $this->set("relationExists", $relationExists); 
        $this->render('/Ajax/followbutton/');
    }
           
    public function unfollow($userSlug)
    {        
        $this->layout   = "ajax";        
        $relationExists = false;
        
        if($this->userIsLoggedIn())
        {
            $sessionId = $this->Session->read('Auth.User.User.id');
            $relationExists = !$this->User->UserFollowers->removeRelation($sessionId, $userSlug);
        }  
        
        $this->set("user", array("slug" => $userSlug)); 
        $this->set("relationExists", $relationExists);        
        $this->render('/Ajax/followbutton/');
    }
    
    /** 
     * Lists the complete details of all user notifications. 
     */
    public function notifications()
    {                
        $notifications = $this->Paginator->paginate('Notifications', array('user_id' => $this->Session->read('Auth.User.User.id')));        
        $this->set('notifications', $notifications);        
        $this->setPageTitle(array(__("Recent notifications")));
    }    
    
    /** 
     * Lists the complete details of all user notifications. 
     */
    public function achievements()
    {
        $achievements = $this->User->UserAchievements->findAllByUserId($this->Session->read('Auth.User.User.id'));
        $this->set('achievements', $achievements);        
        $this->setPageTitle(array(__("Unlocked achievements")));
    }  
        
    /** 
     * Builds the default user dashboard
     */
    public function dashboard()
    {
        $userId = (int)$this->Session->read('Auth.User.User.id');
        $data = $this->User->findById($userId);
        
        if(!$data)
        {
            $this->Session->setFlash(__('This user does not exist.'), 'Flash'.DS.'failure');
            $this->redirect(array('controller' => 'pages', 'action' => 'error'));
        }                
        
        $this->loadModel("Track");
        $dailyChallenge = $this->Track->findDailyChallenge();        
        $topAreas       = $this->Track->TrackReviewSnapshot->getTopAreasByUserId($userId);
        $recentReviews  = $this->Track->TrackReviewSnapshot->getRecentReviewsByUserId($userId, 5);
                
        foreach($recentReviews as $idx => $review)
        {
            $recentReviews[$idx]["appreciation"] = $this->Track->TrackReviewSnapshot->getUserAppreciation($review["Track"]["id"], $userId);
        }
        
        $this->loadModel("Album");
        $weekDate = mktime(0, 0, 0, date("n"), date("j") - date("N"));
        $this->set("newReleases", $this->Album->getNewReleases(3));
        $this->set("forTheWeekOf", $weekDate);
        $this->set("dailyChallenge", $dailyChallenge);
        $this->set("user",          $data['User']);                
        $this->set("recentReviews", $recentReviews);
        $this->set("topAreas",      $topAreas);
        
        $this->setPageTitle(array(__("TMT dashboard")));
    }
    
    public function followers($userSlug = null)
    {   
        if(!$this->userIsLoggedIn() && is_null($userSlug))
        {
            $this->Session->setFlash(__('This user does not exist.'), 'Flash'.DS.'failure');
            $this->redirect(array('controller' => 'pages', 'action' => 'error'));            
        }
        
        $userId = (int)$this->Session->read('Auth.User.User.id');
        
        if(!is_null($userSlug))
        {  
            $data = $this->User->findBySlug($userSlug);
            if(!$data)
            {
                $this->Session->setFlash(__('This user does not exist.'), 'Flash'.DS.'failure');
                $this->redirect(array('controller' => 'pages', 'action' => 'error'));
            }  
            $userId = $data["User"]["id"];
        }
        
        $this->set("followers", $this->User->UserFollowers->getFollowers($userId));
        
    }
    
    public function following($userSlug = null)
    {
        if(!$this->userIsLoggedIn() && is_null($userSlug))
        {
            $this->Session->setFlash(__('This user does not exist.'), 'Flash'.DS.'failure');
            $this->redirect(array('controller' => 'pages', 'action' => 'error'));            
        }
        
        $userId = (int)$this->Session->read('Auth.User.User.id');
        
        if(!is_null($userSlug))
        {  
            $data = $this->User->findBySlug($userSlug);
            if(!$data)
            {
                $this->Session->setFlash(__('This user does not exist.'), 'Flash'.DS.'failure');
                $this->redirect(array('controller' => 'pages', 'action' => 'error'));
            }  
            $userId = $data["User"]["id"];
        }
                
        $this->set("following", $this->User->UserFollowers->getFollowing($userId));
    }
    
    
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
       
    
    /** 
     * Edit the current user's details
     */
    public function edit()
    {
        $user = $this->Session->read('Auth.User.User'); 
        $saved = false;
        
        if(!$user)
        {
            $this->Session->setFlash(__('You cannot edit your account if you are not logged in'), 'Flash'.DS.'failure');
            $this->redirect(array('controller' => 'pages', 'action' => 'error'));
        }
        
        if($this->request->is("put"))
        {       
            $saved = $this->User->save($this->request->data["User"]);
            ($saved) ?
                $this->Session->setFlash(__('Your information has been saved!'), 'Flash'.DS.'success') :
                $this->Session->setFlash(__('We could not save your information'), 'Flash'.DS.'failure');
        }
        
        
        $data = $this->User->findById((int)$user["id"], array('User.*', 'RdioUser.*', 'FacebookUser.*'));  
        if($saved) $this->updateUserSession($data);
        $this->request->data = $data;
        
        $hasRdio = ($data['RdioUser']['id']);
        $hasFacebook = ($data['FacebookUser']['id']);
        $hasAccount = ($data['User']['username'] && $data['User']['password']);
        
        $apis = array();        
        $apis[] = __("Mp3");
        if($hasRdio) $apis[] = __("Rdio");        
        $this->set("availableApis", $apis);
        
        $this->set("hasRdio", $hasRdio);      
        $this->set("hasFacebook", $hasFacebook);    
        $this->set("hasAccount", $hasAccount);   
        
        $this->setPageTitle(__("Edit profile"));
    }        
    
    /** 
     * Create a new user page
     */
	public function create()
	{	
		if ($this->request->is('post') && array_key_exists('User', $this->data))
		{	
			if($this->data['User']['password'] == $this->data['User']['password_confirm'])
			{					                
                $this->User->create();
                if($this->User->saveAll($this->request->data))   
                {   
                    $data = $this->User->read(null, $this->User->id);
                    $this->startUserSession($data);                    
                    $this->redirectByRURL(array('controller' => 'users', 'action' => 'dashboard'));
                }
				$this->Session->setFlash(__('We could not save this user.'), 'Flash'.DS.'failure');
			}
			else
			{
				$this->Session->setFlash(__('The passwords do not match.'), 'Flash'.DS.'failure');
			}
		}
        
        $this->setPageTitle(__("Create profile"));
	}
        
    /** 
     * Login page
     */
	public function login()
	{		
        $this->set("redirectUrl", $this->request->query("rurl"));  
		if ($this->request->is('post'))
		{
			if($this->Auth->login())
            {                                
                $this->redirectByRURL(array('controller' => 'users', 'action' => 'dashboard'));
            }
				
            $this->Session->setFlash(__('Invalid username or password, try again'));
		}
        
        $this->setPageTitle(__("Login"));
	}    
    
    /** 
     * Creates, logs-in or associates a rdio user to a user profile
     */
    public function checkrdiouser()
    {                  
        if($this->Session->check('Login.User.RdioUser'))
        {
            $user = $this->Session->read('Login.User.RdioUser');   
            // Destroy the temporary rdio user session values
            $this->Session->delete('Login.User.RdioUser');
            
            $data = $this->User->RdioUser->findByKey($user->key);						
            
            if($this->userIsLoggedIn() && !$this->_idsAreOK($data))
            {
                $this->Session->setFlash(__('This Rdio account is already linked to another TMT profile.'), 'Flash'.DS.'failure');                
                $this->redirectByRURL(array('controller' => 'users', 'action' => 'edit'));
            }
            
            if(!$data) $data = $this->_createUserFromRdio($user);
                        
            $this->startUserSession($data);	
            
            if($this->User->RdioUser->requiresUpdate($data))
            {   
                $this->redirectByRURL(array("controller" => "artists", "action" => "syncUserLibrary"), true);
            }
            
            $this->redirectByRURL(array('controller' => 'users', 'action' => 'dashboard'));
        }
        
        $this->Session->setFlash(__('We could not parse your Rdio user.'), 'Flash'.DS.'failure');
        $this->render("index");
    }
    
    /** 
     * Creates, logs-in or associates a facebook user to a user profile
     */
    public function checkfacebookuser()
    {           
        if($this->Session->check('Login.User.FacebookUser'))
        {
            $facebookUser = $this->Session->read('Login.User.FacebookUser');   
            // Destroy the temporary user session values
            $this->Session->delete('Login.User.FacebookUser');
        
            $data = $this->User->FacebookUser->find('first', array("conditions" => "facebook_id = {$facebookUser->id}"));
           
            if($this->userIsLoggedIn() && !$this->_idsAreOk($data))
            {
                $this->Session->setFlash(__('This Facebook account is already linked to another TMT profile.'), 'Flash'.DS.'failure');
                $this->redirect(array('controller' => 'users', 'action' => 'edit'));
            }
            
            if(!$data) $data = $this->_createUserFromFacebook($facebookUser);
            
            $this->startUserSession($data);            
            $this->redirectByRURL(array('controller' => 'users', 'action' => 'dashboard'));
        }
        
        $this->Session->setFlash(__('We could not parse your Facebook user.'), 'Flash'.DS.'failure');
        $this->render("index");
    }
        
    /** 
     * Disconnects rdio information to the current profile.
     */
    public function disconnectRdio()
    {
        $this->RdioApi->clearSession();
        $this->User->RdioUser->deleteAll(array("user_id" => $this->getAuthUserId())) ?
            $this->Session->setFlash(__('You have revoked our access to your Rdio account.'), 'Flash'.DS.'success') :
            $this->Session->setFlash(__('We could not revoke our access to your Rdio account.'), 'Flash'.DS.'failure');
        
        $this->redirect(array("controller" => "users", "action" => "edit"));
    }
    
    /** 
     * Disconnects Facebook information to the current profile.
     */
    public function disconnectFacebook()
    {
        $this->User->FacebookUser->deleteAll(array("user_id" => $this->getAuthUserId())) ?
            $this->Session->setFlash(__('You have revoked our access to your Facebook account.'), 'Flash'.DS.'success') :
            $this->Session->setFlash(__('We could not revoke our access to your Facebook account.'), 'Flash'.DS.'failure');
        
        $this->Session->setFlash(__('You have revoked our access to your Facebook account.'), 'Flash'.DS.'success');
        $this->redirect(array("controller" => "users", "action" => "edit"));
    }    
    
    /** 
     * Logout page
     */
	public function logout()
	{		
        $this->destroyUserSession();
		$this->Session->setFlash(__('You have been successfully logged out.'), 'Flash'.DS.'success');
        
        $this->setPageTitle(__("Logout"));
	}
    
    private function _createUserFromRdio($user)
    {        
        // Create a new user from the Rdio profile
        $formattedData = array(
            "image_src"      => $user->icon,
            "image"         => $this->User->getImageFromUrl($user->icon),
            "prefered_player_api" => 1,
            "RdioUser"  => array("key" => $user->key)
        );
        
        if($this->userIsLoggedIn())
        {            
            $user = $this->getAuthUser();
            $formattedData["id"] = $user["id"];
            $formattedData["slug"] = $user["slug"];
        }
        else
        {   
            $formattedData["firstname"] = $user->firstName;
            $formattedData["lastname"]  = $user->lastName;
            $this->User->create();
        }
        
        if($this->User->saveAll($formattedData))    
        {   
            return $this->User->read(null, $this->User->id);
        }
    }
        
    private function _createUserFromFacebook($user)
    {
        // Create a new user from the Rdio profile
        $formattedData = array(
            "FacebookUser"  => array("facebook_id" => $user->id)
        );

        if($this->userIsLoggedIn())
        {            
            $user = $this->getAuthUser();
            $formattedData["id"] = $user["id"];
            $formattedData["slug"] = $user["slug"];
        }
        else
        {
            $formattedData["firstname"] = $user->first_name;
            $formattedData["lastname"]  = $user->last_name;
            $this->User->create();
        }
        
        if($this->User->saveAll($formattedData))    
        {   
            return $this->User->read(null, $this->User->id);
        }
    }
    
    private function _idsAreOk($data)
    {
        return (array_key_exists("User", $data) && (int)$data["User"]["id"] === $this->getAuthUserId());
    }
    
}

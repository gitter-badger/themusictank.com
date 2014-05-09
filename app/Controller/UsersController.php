<?php
/**
 * UsersController controller
 *
 * Contains Users pages methods
 *
 * @package       app.Controller
 */
class UsersController extends AppController {
    
    var $helpers    = array("Chart", "Time");
    var $components = array("Paginator", "FacebookApi", "RdioApi");
    var $paginate = array(
        'limit' => 25,
        'order' => array(
            'Notifications.created' => 'desc'
        )
    );
                 
    public function beforeFilter()
    {   
        parent::beforeFilter();   
        $this->Auth->deny("edit", "dashboard", "notifications", "whatsup", "okstfu");
    }    
     
    /** 
     * Lists the complete details of all user notifications. 
     */
    public function notifications()
    {                
        $notifications = $this->Paginator->paginate('Notifications', array('user_id' => $this->getAuthUserId()));
        $this->set('notifications', $this->User->Notifications->associateRelated($notifications));
        $this->setPageTitle(array(__("Recent notifications")));
    }    
               
    /** 
     * Builds the default user dashboard
     */
    public function dashboard()
    {   
        $this->loadModel("UserActivity");
        $this->loadModel("Track");
                
        $userId = $this->getAuthUserId();
        $list = array_values($this->User->UserFollowers->getSubscriptions($userId));
        $list[] = $userId;
                
        $feed = $this->UserActivity->fetchActivity($list);
        
        $this->set("feed", $feed);
        $this->set("dailyChallenge", $this->Track->findDailyChallenge());
        $this->setPageTitle(array(__("Dashboard")));
    }             
    
    /** 
     * Edit the current user's details
     */
    public function edit()
    {
        $user = $this->getAuthUser();
        $saved = false;
            
        if(!$user)
        {
            throw new NotFoundException('Could not find that user');
        }
        
        if($this->request->is("put"))
        {       
            $saved = $this->User->save($this->request->data["User"]);
            ($saved) ?
                $this->Session->setFlash(__('Your information has been saved!'), 'Flash'.DS.'success') :
                $this->Session->setFlash(__('We could not save your information'), 'Flash'.DS.'failure');
        }
                
        $data = $this->User->findById((int)$user["User"]["id"], array('User.*', 'RdioUser.*', 'FacebookUser.*'));  
        if($saved) $this->updateUserSession($data);
        $this->request->data = $data;
        
        $hasRdio        = ($data['RdioUser']['id']);
        $hasFacebook    = ($data['FacebookUser']['id']);
        $hasAccount     = ($data['User']['username'] && $data['User']['password']);
        
        $apis = array(__("Mp3"));
        if($hasRdio) $apis[] = __("Rdio"); 
        
        $this->set("availableApis", $apis);        
        $this->set("hasRdio",       $hasRdio);      
        $this->set("hasFacebook",   $hasFacebook);    
        $this->set("hasAccount",    $hasAccount);   
        
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
     * Login page. Uses the TMT form.
     */
	public function login()
	{		
        $this->set("redirectUrl", $this->request->query("rurl"));  
		if ($this->request->is('post'))
		{
			if($this->Auth->login())
            {   
                $data = $this->User->findByUsername($this->request->data["User"]["username"]);
                $this->startUserSession($data); 
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
            
            if($this->userIsLoggedIn())
            {
                // No rdio user match in the db
                if(!$data)
                {
                    // If user is logged somehow, send in the current session values
                    // so the api can link the objects
                    $data = $this->User->RdioUser->createFromAPI($user, $this->userIsLoggedIn() ? $this->getAuthUser() : null);                  
                }
                // There is a match and the user is logged somehow, but ids do not match
                else if(!$this->_idsAreOK($data))
                {
                    $this->Session->setFlash(__('This Rdio account is already linked to another TMT profile.'), 'Flash'.DS.'failure');                
                    $this->redirectByRURL(array('controller' => 'users', 'action' => 'edit'));            
                }
            }
            else if(!$data)
            {
                // if user is not logged and there is no match, create the account.
                $data = $this->User->RdioUser->createFromAPI($user); 
            }
                
            // Now, data should be up to date.
            if($data)
            {        
                !$this->userIsLoggedIn() ? $this->startUserSession($data) : $this->updateUserSession($data);                
                $this->User->RdioUser->data = $data;
                $this->User->RdioUser->updateCached();
                $this->redirectByRURL(array('controller' => 'users', 'action' => 'dashboard'));
            }
        }
        
        $this->Session->setFlash(__('We could not parse your Rdio user.'), 'Flash'.DS.'failure');
        $this->redirectByRURL(array('controller' => 'users', 'action' => 'login'));
    }
    
    /** 
     * Creates, logs-in or associates a facebook user to a user profile
     */
    public function checkfacebookuser()
    {           
        if($this->Session->check('Login.User.FacebookUser'))
        {
            $user = $this->Session->read('Login.User.FacebookUser');   
            // Destroy the temporary user session values
            $this->Session->delete('Login.User.FacebookUser');
        
            $data = $this->User->FacebookUser->findByFacebookId($user->id);	
           
            // No facebook user match in the db
            if(!$data)
            {
                // If user is logged somehow, send in the current session values
                // so the api can link the objects
                $data = $this->User->FacebookUser->createFromAPI($user, $this->userIsLoggedIn() ? $this->getAuthUser() : null);                  
            }
            // There is a match and the user is logged somehow, but ids do not match
            else if($this->userIsLoggedIn() && !$this->_idsAreOK($data))
            {
                $this->Session->setFlash(__('This Facebook account is already linked to another TMT profile.'), 'Flash'.DS.'failure');                
                $this->redirectByRURL(array('controller' => 'users', 'action' => 'edit'));            
            }
                         
            // Now, data should be up to date.
            if($data)
            {        
                if($this->userIsLoggedIn()) {
                    $this->updateUserSession($data);
                    $this->redirect(array('controller' => 'users', 'action' => 'edit'));
                }
                else
                {
                    $this->startUserSession($data);                
                    $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
                }
            }
        }
        
        $this->Session->setFlash(__('We could not parse your Facebook user.'), 'Flash'.DS.'failure');
        $this->redirectByRURL(array('controller' => 'users', 'action' => 'login'));
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
        
    private function _idsAreOk($data)
    {
        return (array_key_exists("User", $data) && (int)$data["User"]["id"] === $this->getAuthUserId());    
    }
    
}

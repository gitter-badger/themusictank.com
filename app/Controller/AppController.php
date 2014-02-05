<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file.
 */
App::uses('Controller', 'Controller');
App::uses('AchievementListener', 'Lib');
App::uses('ActivityListener', 'Lib');

/**
 * Application Controller
 *
 * Application-wide controller methods 
 *
 * @package		app.Controller
 */
class AppController extends Controller {
    
  	public $components = array(
		'Session',
		'Auth' => array(
			'authorize' => array('Controller'),
			'loginAction' => array('admin' => false, 'controller' => 'users', 'action' => 'login')
		)        
	);	
    
    public $preferredPlayer = null;
    
    public function beforeFilter()
    {
        $this->Auth->allow();
        parent::beforeFilter();
        
        // Include models that are used globally
        $this->loadModel("User");
    }
                    
    /** 
     * Logs in a user with the Auth component
     *
     * @param array $user User dataset
     * @return void
     */
	public function startUserSession($user)
	{        
		$this->Auth->login($user);
	}
             
    /** 
     * Updates the stored user session
     *
     * @param array $user User dataset
     * @return void
     */
    public function updateUserSession($user)
    {
        foreach($user as $key => $data)
        {
            $this->Session->write('Auth.User.' . $key, $data);
        }
    }
    
    /** 
     * Returns current user login state
     *
     * @return boolean True when user is logged, false if user is not
     */
	public function userIsLoggedIn()
	{        
        return !is_null($this->getAuthUser());
	}
    
    /** 
     * Returns current user session data
     *
     * @return array User dataset
     */
    public function getAuthUser()
    {
        return $this->Session->read('Auth.User');
    }
    
    /** 
     * Returns the id of the user in current session
     *
     * @return int Current user id
     */
    public function getAuthUserId()
    {
        $user = $this->getAuthUser();
        return (int)$user["User"]["id"];
    }    
             
    /** 
     * Destroys the stored user session
     *
     * @return void
     */
    public function destroyUserSession()
    {
		$this->Auth->logout();
		$this->Session->destroy();
    }
    
    /** 
     * This forces Cakephp to allow public pages when the Auth component is used.
     *
     * @return boolean Always true
     */
    public function isAuthorized($user)
    {
        return true;
    }    
    
    /** 
     * Redirect a user based on a default destination URL, or
     * one that may be attached with the ?rurl= query parameter.
     *
     * @param string $destination Default destination url
     * @param boolean $appendRedirect Chose to automatically append the rurl parameter or not. Optional. Defaults to false. 
     * @return void
     */
    public function redirectByRURL($destination, $appendRedirect = false)
    {
        $rurl = $this->request->query("rurl");
        
        if(!$rurl)              $this->redirect($destination);
        if(!$appendRedirect)    $this->redirect($rurl);
        
        $destination["?"] = array("rurl" => $rurl);
        $this->redirect($destination);
    }    
        
    /** 
     * Automates page title creation.
     *
     * @param mixed $steps A string or array of strings the make the title.
     * @return void
     */
    public function setPageTitle($steps)
    {
        if(is_array($steps))
        {
            $steps = implode(" &mdash; ", $steps);
        }
        
        $this->set("title_for_layout", $steps);
    }
        
    /** 
     * Automates page meta creation.
     *
     * @param array $meta An array of meta information. Directly uses Cakephp's
     * way of doing it
     * @return void
     */
    public function setPageMeta($meta)
    {        
        $this->set('meta_for_layout', $meta);
    }
    
    /** 
     * Prepares all the flags to load the proper player on a page
     * @return void
     */
    public function usesPlayer($isReview = false)
    {        
        $preferredPlayer = "mp3";
        if($this->userIsLoggedIn())
        {
            $user = $this->getAuthUser();
            $preferredPlayer = User::getPreferredPlayer($user["User"]);
        }
        $this->preferredPlayer = $preferredPlayer;
        $this->set("preferredPlayer", $preferredPlayer);
        $this->set("isReview", $isReview);
    }
        
}

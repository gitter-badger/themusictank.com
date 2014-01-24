<?php
/**
 * AlbumsController controller
 *
 * Contains Album pages methods
 *
 * @package       app.Controller
 */
class AjaxController extends AppController {
                 
    public function beforeFilter()
    {   
        $this->layout   = "ajax";   
        parent::beforeFilter();   
        $this->Auth->deny("whatsup", "okstfu", "follow", "unfollow");
    }
    
    /** 
     * Json call that lists recent Notifications for the current user
     */
    public function whatsup()
    {       
        $this->_setSessionNotifications();         
        $this->render('dropdownnotifications');
    }
    
    /** 
     * Json call that changes the status of notifications to 'read'
     */
    public function okstfu()
    {          
        $this->User->Notifications->markAsRead(time());     
        $this->_setSessionNotifications();
        $this->render('dropdownnotifications');
    }
        
    public function follow($userSlug)
    {     
        $relationExists = false;
        
        if($this->userIsLoggedIn())
        {
            $relationExists = $this->User->UserFollowers->addRelation($this->getAuthUserId(), $userSlug);
        }  
        
        $this->set("user", array("slug" => $userSlug, "currently_followed" => $relationExists)); 
    }
           
    public function unfollow($userSlug)
    {        
        $this->layout   = "ajax";        
        $relationExists = false;

        if($this->userIsLoggedIn())
        {
            $relationExists = !$this->User->UserFollowers->removeRelation($this->getAuthUserId(), $userSlug);
        }  

        $this->set("user", array("slug" => $userSlug, "currently_followed" => $relationExists)); 
    }
    
    private function _setSessionNotifications()
    {
        $this->set("notifications", $this->User->Notifications->findByUserId($this->getAuthUserId(), 5));            
    }
    
}
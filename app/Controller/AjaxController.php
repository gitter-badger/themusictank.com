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
        $this->set("notifications", $this->User->Notifications->findByUserId($this->getAuthUserId(), 5));    
        $this->render('dropdownnotifications');
    }
    
    /** 
     * Json call that changes the status of notifications to 'read'
     */
    public function okstfu()
    {          
        $this->User->Notifications->markAsRead(time());     
        $this->set("notifications", $this->User->Notifications->findByUserId($this->getAuthUserId(), 5));    
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
        $relationExists = false;

        if($this->userIsLoggedIn())
        {
            $relationExists = !$this->User->UserFollowers->removeRelation($this->getAuthUserId(), $userSlug);
        }  

        $this->set("user", array("slug" => $userSlug, "currently_followed" => $relationExists)); 
    }
        
    public function oembed() {    
        $this->response->type('application/json');
        
        if(!array_key_exists("url", $this->request->query))
            throw new NotFoundException();
        
        $url = $this->request->query["url"];
        $pattern = explode("/", preg_replace('/http:\/\//', "", $url));
        $model = $pattern[1];
        $slug = $pattern[3];
        
        if(!preg_match('/albums|tracks/', $model))
            throw new NotFoundException();
        
        $modelName = substr(ucfirst($model), 0, -1);
        $this->loadModel($modelName);        
        $instance = new $modelName();
        $instance->getUpdatedSetBySlug($slug);
                        
        $defaults = array(
            "version"   => "1.0",
            "type"      => "rich",
            "provider_name" => "The Music Tank",
            "provider_url"=> sprintf("http://%s/", $_SERVER['SERVER_NAME']),
        );
                        
        $this->set("jsonOutput", array_merge($defaults, $instance->toOEmbed()));
    }
    
}
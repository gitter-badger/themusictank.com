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
        $this->Auth->deny("whatsup", "okstfu", "follow", "unfollow", "pushrf");
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
        $relationExists = $this->userIsLoggedIn() && $this->User->UserFollowers->addRelation($this->getAuthUserId(), $userSlug);        
        $this->set("user", array("slug" => $userSlug, "currently_followed" => $relationExists)); 
    }
           
    public function unfollow($userSlug)
    {              
        $relationExists = $this->userIsLoggedIn() && !$this->User->UserFollowers->removeRelation($this->getAuthUserId(), $userSlug);
        $this->set("user", array("slug" => $userSlug, "currently_followed" => $relationExists)); 
    }
        
    public function oembed() {    
        $this->response->type('application/json');
        
        if(!array_key_exists("url", $this->request->query))
        {
            throw new NotFoundException();
        }
                          
        $instance = $this->_loadObjectFromOEmbededUrl($this->request->query["url"]);                      
                
        if(!$instance->data)
        {
            throw new NotFoundException();
        }
        
        $this->set("jsonOutput", $instance->toOEmbed());
        $this->render('index');
    }
    
    /** 
     * Push review frames while reviewing
     */
    public function pushrf($keys, $shaCheck)
    {
        $this->response->type('application/json');
        
        $keyMapping = explode("-", $keys);
        $userId = $this->getAuthUserId(); 
        $validSha =  sha1($userId . (int)$keyMapping[2] . "user is reviewing something kewl");
        
        if($userId !== (int)$keyMapping[3])
        {
            throw new NotFoundException(__("This is not your user."));
        }        
        elseif(!array_key_exists("frames", $this->request->data))
        {
            throw new NotFoundException(__("Request is missing frames."));
        }
        elseif($shaCheck == $validSha)
        {
            $this->loadModel("ReviewFrame");
            $this->set("jsonOutput", array("status" => $this->ReviewFrame->savePlayerData($this->request->data["frames"], $keyMapping) ? "success" : "failure"));
        }
        else
        {                
            throw new NotFoundException(__("We don't know where you are from."));
        }    
        $this->render('index');    
    }            
    
    public function savewave($trackSlug, $shaCheck)
    {       
        $this->response->type('application/json');
                        
        $this->loadModel("Track");        
        $data = $this->Track->findBySlug($trackSlug);
        
        if(!$data)
        {
            throw new NotFoundException();
        }
        
        $validSha = sha1($data["Track"]["slug"] . $data["Track"]["id"] . "foraiurtheplayer");        
        if($shaCheck != $validSha)
        {
            throw new NotFoundException(__("We don't know where you are from."));
        }
        
        $this->Track->data = $data;
        $this->set("jsonOutput", $this->Track->saveWave($this->request->data["waves"]));
        $this->render('index');
    }
    
    
    private function _loadObjectFromOEmbededUrl($url)
    {
        $pattern = explode("/", preg_replace('/http:\/\//', "", $url));
        
        if(count($pattern) < 3 && count($pattern) > 4)
        {
            throw new NotFoundException();
        }
                
        $model = $pattern[1];
        $slug = $pattern[3];

        if(!preg_match('/albums|tracks/i', $model))
        {
            throw new NotFoundException();
        }
                
        $modelName = substr(ucfirst($model), 0, -1);
        $this->loadModel($modelName);
        
        $instance = new $modelName();        
        $instance->getUpdatedSetBySlug($slug);    
        
        return $instance;
    }
    
}
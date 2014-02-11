<?php
/**
 * ReviewFramesController controller
 *
 * Contains ReviewFrames pages methods
 *
 * @package       app.Controller
 */ 
class ReviewFramesController extends AppController {
              
    public function beforeFilter()
    {   
        parent::beforeFilter();   
        $this->Auth->deny("save");        
    }    
    
    /** 
     * Listens to posted reviewframes values.
     *
     * @param array $keys An array of reviewframe values
     * @return json $reponse
     */
    public function save($keys, $shaCheck)
    {
        $this->layout = "ajax";
        
        $response = array();
        $keyMapping = explode("-", $keys);
        $userId = $this->getAuthUserId(); 
        
        if($userId !== (int)$keyMapping[3])
        {
            throw new NotFoundException(__("This is not your user."));
        }        
        elseif(!array_key_exists("frames", $this->request->data))
        {
            throw new NotFoundException(__("Request is missing frames."));
        }
        else
        {              
            $validSha =  sha1($userId . (int)$keyMapping[2] . "user is reviewing something kewl");
            if($shaCheck != $validSha)
            {
                throw new NotFoundException(__("We don't know where you are from."));
            }            
            
            $response["saved"] = $this->ReviewFrame->savePlayerData($this->request->data["frames"], $keyMapping);
        }
        
        $this->set('response', $response);
        $this->render('/Pages/json/');
    }
    
}
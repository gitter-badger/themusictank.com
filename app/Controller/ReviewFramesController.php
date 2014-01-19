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
    public function save($keys)
    {           
        $this->layout = "ajax";
        
        $response = array();
        $keyMapping = explode("-", $keys);
        $userId = $this->getAuthUserId(); 
        
        if($userId !== (int)$keyMapping[3])
        {
            $response["error"] = true;
            $response["msg"] = __("User session not valid.");
        }        
        elseif(!array_key_exists("frames", $this->request->data))
        {
            $response["error"] = true;
            $response["msg"] = __("Not a valid request.");
        }
        else
        {  
            // wtf am i doing here
            $this->loadModel("ReviewFrames");
            $response["saved"] = $this->ReviewFrames->savePlayerData($this->request->data["frames"], $keyMapping);
        }
        
        $this->set('response', $response);
        $this->render('/Pages/json/');
    }
}
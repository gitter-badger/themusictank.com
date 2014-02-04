<?php

class PlayerController extends AppController {
    
      
    public function beforeFilter()
    {   
        parent::beforeFilter();         
        $this->Auth->deny(array("rdio", "mp3"));
        $this->usesPlayer(true);
    }
                            
    /** 
     * Player selection page. Automatically forwards to the prefered api and logs the user in.
     *
     * @param string $artistSlug Artist slug
     * @param string $albumSlug Album slug
     * @param string $trackSlug Track slug
     */
    public function play($trackSlug)
    {       
        if($this->userIsLoggedIn())
        {
            $user = $this->getAuthUser();
            if(User::getPreferredPlayer($user["User"]) === User::PLAYER_RDIO)
            {
                $this->redirect(array("controller" => "player", "action" => "rdio", $trackSlug));
            }
            else
            {
                $this->redirect(array("controller" => "player", "action" => "mp3", $trackSlug));            
            }
        }
        
        $this->redirect(array("controller" => "users", "action" => "login", "?" => array("rurl" => "/player/play/$trackSlug")));         
        
        $this->setPageTitle(array( sprintf(__("Reviewing %s"), $data["Track"]["title"]), $data["Album"]["name"], $data["Album"]["Artist"]["name"]));        
        $this->setPageMeta(array(
            "keywords" => array(__("Review"), __("mp3"), __("Rdio"), $data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]),
            "description" => 
                sprintf(__("Reviewing %s, a track featured on %s, an album by %s that was released on %s"), 
                    $data["Track"]["title"], 
                    $data["Album"]["name"], 
                    $data["Album"]["Artist"]["name"], 
                    date("F j Y", $data["Album"]["release_date"])
                )
        ));
    }
                        
    /** 
     * Rdio web playback player page
     *
     * @param string $artistSlug Artist slug
     * @param string $albumSlug Album slug
     * @param string $trackSlug Track slug
     */
    public function rdio($trackSlug)
    {              
        $this->loadModel("Track");
        $data = $this->Track->getBySlugContained($trackSlug);
                
        $this->set("track",     $data["Track"]); 
        $this->set("rdioTrack", $data["RdioTrack"]);   
        $this->set("album",     $data["Album"]);     
        $this->set("artist",    $data["Album"]["Artist"]);   
        $token = $this->Session->read('Player.RdioPlaybackToken');
        
        if($this->Session->check('Player.Rdio'))
        { 
            if(!$token)
            {
                $token = $this->User->RdioUser->getPlaybackToken();
                $this->Session->write('Player.RdioPlaybackToken', $token);
            }
            
            $this->set("playbackToken", $token);
            
        } else throw new NotFoundException(__('We could not connect with Rdio.'));
                        
        $this->setPageTitle(array( sprintf(__("Reviewing %s"), $data["Track"]["title"]), $data["Album"]["name"], $data["Album"]["Artist"]["name"]));        
        $this->setPageMeta(array(
            "keywords" => array(__("Review"), __("Rdio"), $data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]),
            "description" => 
                sprintf(__("Reviewing %s, a track featured on %s, an album by %s that was released on %s"), 
                    $data["Track"]["title"], 
                    $data["Album"]["name"], 
                    $data["Album"]["Artist"]["name"], 
                    date("F j Y", $data["Album"]["release_date"])
                )
        ));
    }
                            
    /** 
     * Mp3 web playback player page
     *
     * @param string $artistSlug Artist slug
     * @param string $albumSlug Album slug
     * @param string $trackSlug Track slug
     */
    public function mp3($trackSlug)
    {
        $this->loadModel("Track");     
        
        $data = $this->Track->getBySlugContained($trackSlug);
        $this->set("track",     $data["Track"]); 
        $this->set("album",     $data["Album"]);     
        $this->set("artist",    $data["Album"]["Artist"]);   
                          
        $this->setPageTitle(array( sprintf(__("Reviewing %s"), $data["Track"]["title"]), $data["Album"]["name"], $data["Album"]["Artist"]["name"]));        
        $this->setPageMeta(array(
            "keywords" => array(__("Review"), __("Mp3"), $data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]),
            "description" => 
                sprintf(__("Reviewing %s, a track featured on %s, an album by %s that was released on %s"), 
                    $data["Track"]["title"], 
                    $data["Album"]["name"], 
                    $data["Album"]["Artist"]["name"], 
                    date("F j Y", $data["Album"]["release_date"])
                )
        ));
    }
}

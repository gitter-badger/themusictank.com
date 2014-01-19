<?php
/**
 * PlayerController controller
 *
 * Contains Player pages methods
 *
 * @package       app.Controller
 */ 

App::uses('Controller', 'Controller');
$vendor = App::path('Vendor');        
require_once ($vendor[0] . "rdio-simple/rdio.php"); 

class PlayerController extends AppController {
    
      
    public function beforeFilter()
    {   
        parent::beforeFilter();
        $this->Auth->deny(array("rdio", "mp3"));
    }
    
    /** 
     * Default fallback when there is an error. Not really used and should probably be removed.
     */
    public function index()
    {
        
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
                                    
            if((int)$user["prefered_player_api"] === 1 && $this->Session->check('Player.Rdio'))
            {
                $this->redirect(array("controller" => "player", "action" => "rdio", $trackSlug));
            }
            else
            {
                $this->redirect(array("controller" => "player", "action" => "mp3", $trackSlug));            
            }
        }
        
        $this->redirect(array("controller" => "users", "action" => "login", "?" => array("rurl" => "/player/play/$trackSlug")));
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
        
        if($this->Session->check('Player.Rdio'))
        {
            $this->layout = "player";
            
            $rdio = $this->Session->read('Player.Rdio');
            $tokenResult = $rdio->call('getPlaybackToken', array("domain" => $_SERVER['SERVER_NAME']));
                        
            $this->set("playbackToken", $tokenResult->result);
            
        } else $this->Session->setFlash(__('We could not connect with Rdio.'), 'Flash'.DS.'failure');
                        
        $this->setPageTitle(array(__("Reviewing") . ": " . $data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]));        
        $this->setPageMeta(array(
            "keywords" => array(__("Review"), __("Rdio"), $data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]),
            "description" => __("Reviewing ") . $data["Track"]["title"] . __(", a track featured on ") . $data["Album"]["name"] . __(", an album by ") . $data["Album"]["Artist"]["name"] . _(' released ') . $data["Album"]["release_date"] . "."
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
        $this->layout = "player";
        $this->loadModel("Track");     
        
        $data = $this->Track->getBySlugContained($trackSlug);
        $this->set("track",     $data["Track"]); 
        $this->set("album",     $data["Album"]);     
        $this->set("artist",    $data["Album"]["Artist"]);   
                 
        $this->setPageTitle(array(__("Reviewing") . ": " . $data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]));        
        $this->setPageMeta(array(
            "keywords" => array(__("Review"), __("Mp3"), $data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]),
            "description" => __("Reviewing ") . $data["Track"]["title"] . __(", a track featured on ") . $data["Album"]["name"] . __(", an album by ") . $data["Album"]["Artist"]["name"] . _(' released ') . $data["Album"]["release_date"] . "."
        ));
    }
}

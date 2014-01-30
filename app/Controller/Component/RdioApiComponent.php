<?php

$vendor = App::path('Vendor');        
require_once ($vendor[0] . "rdio-simple/rdio.php"); 

/**
 * Description of RdioComponent
 *
 * @author ffaubert
 */
class RdioApiComponent extends Component {
	
	private $_controller;
    private $_instance;	
	public  $components = array('Session');	
	
	public function initialize(Controller $controller)
	{
		$this->_controller = $controller;
	}

    public function clearSession()
    {
        $this->Session->delete('Player.Rdio');
        $this->Session->delete('Player.RdioPlaybackToken');
    }    
    
    public function isInSession()
    {
        return $this->Session->check('Player.Rdio');
    }
    
    public function authenticate($currentUrl = null)
    {
        $this->getInstance();
        $token = $this->Session->read('OAuth.Rdio.token');
        $secret = $this->Session->read('OAuth.Rdio.secret');
        $this->Session->delete('OAuth.Rdio.token');
        $this->Session->delete('OAuth.Rdio.secret');
                
        if($token && $secret)
        {
            # we have a token in our session, let's use it
            $this->_instance->token = array($token, $secret);   
            if ($this->_controller->request->query['oauth_verifier'])
            {
                # we've been passed a verifier, that means that we're in the middle of
                # authentication.
                $this->_instance->complete_authentication($this->_controller->request->query['oauth_verifier']);                                
                $this->Session->write('Player.Rdio', $this->_instance);
                $this->Session->write('Player.RdioPlaybackToken', $this->getPlaybackToken());
                return $this->_instance;
            }
        }
        else
        {   
            # we have no authentication tokens.
            # ask the user to approve this app
            $authorize_url = $this->_instance->begin_authentication( $currentUrl );
            # save the new token in our session
            $this->Session->write('OAuth.Rdio.token', $this->_instance->token[0]);
            $this->Session->write('OAuth.Rdio.secret', $this->_instance->token[1]);
      
            $this->_redirect($authorize_url);
            return true;
        }
        
        return false;
    }
    
    public function getInstance()
    {
        if($this->isInSession())
        {
            $this->_instance = $this->Session->read('Player.Rdio');
        }
        else if(!isset($this->_instance))
        {            
            $this->_instance = new Rdio(Configure::read('RdioApiConfig'));  
        }
        
        return $this->_instance;
    }
    
    public function getUserData()
    {
        $this->getInstance();
        $data = $this->_instance->call('currentUser');   
        return ($data) ? $data->result : null;
    }
        
    public function getArtistLibrary()
    {
        $this->getInstance();
        $data = $this->_instance->call('getArtistsInCollection');   
        return ($data) ? $data->result : null;
    }
    
    public function getHeavyRotation($type = "artists")
    {
        $this->getInstance();
        $data = $this->_instance->call('getHeavyRotation', array("type" => $type));   
        return ($data) ? $data->result : null;
    }
        
    public function getNewReleases($time = "thisweek")
    {
        $this->getInstance();
        $data = $this->_instance->call('getNewReleases', array("time" => $time, "extras" => "tracks"));  
        return ($data) ? $data->result : null;
    }
    
    public function getPlaybackToken()
    {
        $this->getInstance();
        $data = $this->_instance->call('getPlaybackToken', array("domain" => $_SERVER['SERVER_NAME']));  
        return ($data) ? $data->result : null;
    }
    
    private function _redirect($url)
    {
        $this->_controller->redirect($url);
    }
    
    
}
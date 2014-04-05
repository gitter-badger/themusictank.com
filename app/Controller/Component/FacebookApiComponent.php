<?php

class FacebookApiComponent extends Component {
	
	private $controller;
	
	var $components = array('Session');	
	
	public function initialize(Controller $controller)
	{
		$this->controller = $controller;
	}

	public function getUser($currentUrl = null)
	{	
		if(!array_key_exists("code", $_REQUEST))
		{
			$state = md5(uniqid(rand(), TRUE));
			$this->Session->write('Facebook.state', $state); //CSRF protection			
			$this->_redirect($this->_getOAuthUrl($state, $currentUrl));
		}				
		
		$state = $this->Session->read('Facebook.state');		
		if($state && array_key_exists("state", $_REQUEST) && ($state === $_REQUEST['state']))
		{	
			// Dont need the session variable anymore. Unset
			// in case the process is started a second time
			$this->Session->delete('Facebook.state');			
			$tokenInfo = $this->_getToken($_REQUEST["code"], $currentUrl);
			
			if(array_key_exists("access_token", $tokenInfo))
			{
				return $this->_getGraph($tokenInfo['access_token']);
			}
			else
			{
				throw new Exception(__("Could not obtain a token from Facebook (https request was not valid)"));
			}
		}
		else
		{			
			throw new Exception(__("The state does not match. You may be a victim of CSRF."));
		}
		
		return null;
	}
	
	private function _getGraph($token)
	{
		return json_decode(file_get_contents("https://graph.facebook.com/me?access_token={$token}"));
	}
	
	private function _getToken($fbCode, $currentUrl)
	{        
		$response = file_get_contents($this->_getTokenUrl($currentUrl) . "&code=" . $fbCode);
		$params = null;
		parse_str($response, $params);			
		return $params;
	}
	
	private function _getTokenUrl($currentUrl)
	{
		return "https://graph.facebook.com/oauth/access_token"
				. "?client_id=" . Configure::read('FacebookApiConfig.appId') 
				. "&redirect_uri=" . urlencode($currentUrl)
				. "&client_secret=" . Configure::read('FacebookApiConfig.secret');
	}
	
	private function _getOAuthUrl($state, $currentUrl)
	{
		return "https://www.facebook.com/dialog/oauth"
				. "?client_id=" . Configure::read('FacebookApiConfig.appId') 
				. "&redirect_uri=" . urlencode($currentUrl)
				. "&state=" . $state
				. "&scope=email";
	}
	
	private function _redirect($url)
	{
		$this->controller->redirect($url);
	}
	
}
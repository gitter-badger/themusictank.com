<?php
class ApisController extends AppController {
        
	var $components = array("FacebookApi", "RdioApi");
    
    public function connectRdio()
    {
        try {
            
            $this->RdioApi->clearSession();
            if($this->RdioApi->authenticate( $this->_getRedirectUrl() ) !== false)
            {                              
                $this->redirectByRURL(array("controller" => "apis", "action" => "rdioLogin"), true);
            }

            $this->Session->setFlash(__('We could not connect with Rdio.'), 'Flash'.DS.'failure');
        }        
        catch(Exception $e)
        {
            $this->Session->setFlash($e->getMessage(), 'Flash'.DS.'failure');
        }
        
        $this->redirectByRURL(array("controller" => "users", "action" => "login"), true);
    }
    
    public function connectFacebook()
	{	
        try {    
            
            $facebookUser = $this->FacebookApi->getUser( $this->_getRedirectUrl() );
            if($facebookUser)
            {                
                $this->Session->write('Login.User.FacebookUser', $facebookUser);      
                $this->redirectByRURL(array("controller" => "users", "action" => "checkfacebookuser"), true);
            }
            $this->Session->setFlash(__('We could not connect with Facebook.'), 'Flash'.DS.'failure');
        }
        catch(Exception $e)
        {
            $this->Session->setFlash($e->getMessage(), 'Flash'.DS.'failure');
        }
        
        $this->redirectByRURL(array("controller" => "users", "action" => "login"), true);
	}
    
    
    public function rdioLogin()
    {    
        try {        
            if($this->RdioApi->isInSession())
            {
                $this->Session->write('Login.User.RdioUser', $this->RdioApi->getUserData());  
                $this->redirectByRURL(array("controller" => "users", "action" => "checkrdiouser"), true);
            }

            $this->Session->setFlash(__('We could not connect with Rdio.'), 'Flash'.DS.'failure');            
        }
        catch(Exception $e)
        {
            $this->Session->setFlash($e->getMessage(), 'Flash'.DS.'failure');
        }
        
        $this->redirect(array("controller" => "users", "action" => "login"));
    }
    
    private function _getRedirectUrl()
    {
        $redirectUrl = Router::url(null, true);
        if($this->request->query("rurl"))
        {
            $redirectUrl .= "?rurl=" . $this->request->query("rurl");
        }
        
        return $redirectUrl;
    }
    
}

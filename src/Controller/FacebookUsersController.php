<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;
use Cake\Core\Configure;
use Cake\Network\Http\Client;
use Cake\ORM\TableRegistry;

use App\Model\Entity\FacebookUser;
use App\Model\Entity\User;

class FacebookUsersController extends AppController {

    const FACEBOOK_STATE_SESSION_KEY = 'Facebook.state';
    const OAUTH_URL_PATTERN = "https://www.facebook.com/dialog/oauth?client_id=%s&redirect_uri=%s&state=%s&scope=email";
    const ACCESS_TOKEN_URL = "https://graph.facebook.com/oauth/access_token";
    const GRAPH_URL = "https://graph.facebook.com/me";

    public function connect()
    {
        // When there is no facebook code yet, forward to Facebook
        if (!$this->request->query("code")) {
            // To ensure the first forward is a clean process, ensure there's
            // no failed attempt stored in the sessions
            if ($this->Session->check(self::FACEBOOK_STATE_SESSION_KEY)) {
                $this->_triggerError("The page state is expired.");
                return;
            }

            $this->_startOauth();
            return;
        }

        // When there is a code, proceed by confirming login
        elseif ($this->request->query("code")) {
            // Confirm the state of the request makes sense to us
            $predictedState = $this->Session->read(self::FACEBOOK_STATE_SESSION_KEY);
            if(!$predictedState || $this->request->query("state") !== $predictedState) {
                $this->_triggerError("The page state is not the one we expected. You may be a victim of CSRF.");
                return;
            }

            $this->_oauthToken();
            return;
        }

        $this->_triggerError("There is an unexpected issue with Facebook login.");
    }

    protected function _startOauth()
    {
        $redirectUrl    = $this->_getRedirectUrl();
        $csrfState      = md5(uniqid(rand(), true));
        $oauthUrl       = sprintf(self::OAUTH_URL_PATTERN, Configure::read('Apis.facebook.key'), urlencode($redirectUrl), $csrfState);

        // save the csrf for when user comes back form facebook and everything.
        $this->Session->write(self::FACEBOOK_STATE_SESSION_KEY, $csrfState);
        $this->redirect($oauthUrl);
    }

    protected function _oauthToken()
    {
        $facebookCode   = $this->request->query("code");
        $token          = $this->_getToken($facebookCode);

        if(!is_null($token)) {
            $this->_getGraph($token);
            return;
        }

        $this->_triggerError("Facebook did not give us an access token. We can't log you in.");
    }

    protected function _getGraph($facebookToken)
    {
        $userInfo = $this->_getGraphUser($facebookToken);
        if($userInfo) {
            // Dont need the session variable anymore. Unset
            // in case the process is started a second time
            $this->Session->delete(self::FACEBOOK_STATE_SESSION_KEY);

            // Check for a match and create the user if there are no matches
            $fbUser = $this->FacebookUsers->find()->where(["facebook_id" => (int)$userInfo->id])->contain(["Users"])->first();
            if(is_null($fbUser)) {
                $fbUser = new FacebookUser(['facebook_id' => (int)$userInfo->id]);
                $fbUser->user = new User(['firstname' => $userInfo->first_name, 'lastname' => $userInfo->last_name, 'location' => $userInfo->location->name]);
                if (!$this->FacebookUsers->save($fbUser)) {
                    $this->_triggerError("We could not create your account.");
                }
            }

            // login and forward
            if(!is_null($fbUser)) {
                $this->Auth->setUser($fbUser->user->toArray());
                $returnUrl = $this->request->query("rurl");
                $this->redirect(!$returnUrl ? $this->Auth->redirectUrl() : $returnUrl);
                return;
            }
        }

        $this->_triggerError("We could not identify you based on what Facebook sent back.");
    }

    private function _getRedirectUrl()
    {
        return Router::url(['controller' => 'FacebookUsers', 'action' => 'connect', '?' => ['rurl' => $this->request->query("rurl")]], true);
    }

    private function _getToken($facebookCode)
    {
        $http = new Client();
        $queryParams = [
            'client_id' => Configure::read('Apis.facebook.key'),
            'client_secret'    => Configure::read('Apis.facebook.secret'),
            'code'      => $facebookCode,
            'redirect_uri' => $this->_getRedirectUrl(),
        ];
        $request = $http->get(self::ACCESS_TOKEN_URL , $queryParams);

        $params = null;
        parse_str($request->body, $params);

        if(is_array($params) && array_key_exists("access_token", $params)) {
            return $params["access_token"];
        }
    }

    private function _getGraphUser($token)
    {
        $http = new Client();
        $request = $http->get(self::GRAPH_URL, ['access_token' => $token], ['type' => 'json']);
        return json_decode($request->body);
    }

    private function _triggerError($msg)
    {
        $this->Flash->error(__($msg));
        $this->Session->delete(self::FACEBOOK_STATE_SESSION_KEY);
        $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }
}

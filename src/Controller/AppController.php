<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use App\Model\Entity\User;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

/**
 * Components this controller uses.
 *
 * Component names should not include the `Component` suffix. Components
 * declared in subclasses will be merged with components declared here.
 *
 * @var array
 */
	public $helpers = ['Tmt', 'Html'];
    public $components = [
       // 'DebugKit.Toolbar',
        'Flash',
        'Session',
        'Csrf' => ['secure' => true],
        'Auth' => [
            'loginRedirect' => [
                'controller' => 'Profiles',
                'action' => 'dashboard'
            ],
            'logoutRedirect' => [
                'controller' => 'Pages',
                'action' => 'display',
                'home'
            ]
        ]
    ];

    public function beforeFilter(Event $event)
    {
        $this->Auth->allow();

        $userSession = $this->getAuthUser();
        if($userSession) {
            $this->set("userSession", $userSession);
        }
    }

    /**
     * Returns current user login state
     *
     * @return boolean True when user is logged, false if user is not
     */
    public function userIsLoggedIn()
    {
        return !is_null($this->getAuthUser());
    }

    /**
     * Returns current user session data
     *
     * @return array User dataset
     */
    public function getAuthUser()
    {
        $userSessionData = $this->Session->read('Auth.User');
        if($userSessionData) {
            return new User($userSessionData);
        }
    }



}

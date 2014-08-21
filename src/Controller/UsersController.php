<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Utility\Hash;

class UsersController extends AppController {

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.
        $this->Auth->allow(['add', 'logout']);
    }

    public function login()
    {
        $this->set("redirectUrl", $this->request->query("rurl"));
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                $returnUrl = $this->request->query("rurl");
                $this->redirect(!$returnUrl ? $this->Auth->redirectUrl() : $returnUrl);
            }
            $this->Flash->error(__('Invalid username or password, try again'));
        }
    }

    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    public function add()
    {
        $user = $this->Users->newEntity($this->request->data);
        if ($this->request->is('post')) {
            if ($this->Users->save($user)) {
                $this->Flash->success(__('We have created your account.'));
                $this->Auth->setUser($user->toArray());
                return $this->redirect(['controller' => 'profiles', 'action' => 'dashboard']);
            }

            $errors = Hash::extract($user->errors(), "{s}.{s}");
            $this->Flash->error(__('Unable to create your account because of the following error(s): ') . implode("", $errors));
        }
        $this->set('user', $user);
    }

}

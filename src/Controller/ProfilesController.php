<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class ProfilesController extends AppController {

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->deny(['dashboard']);
    }


    public function dashboard()
    {

    }

}

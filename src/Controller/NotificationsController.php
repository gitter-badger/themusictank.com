<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class NotificationsController extends AppController {

    public $components  = ["Paginator"];
    public $paginate    = ['limit' => 15];

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->deny(['index']);
    }

    public function index()
    {
        $userSession= $this->getAuthUser();
        $query      = $this->Notifications->findAllByUserId($userSession->id);
        $results    = $this->Paginator->paginate($query);

        $this->set([
            "notifications" => $results,
            "meta" => [
                "title" => __("Recent notifications")
            ]
        ]);
    }
}

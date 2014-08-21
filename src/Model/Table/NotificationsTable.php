<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use App\Model\Entity\User;

class NotificationsTable extends Table {

    public function initialize(array $config)
    {
        $this->belongsTo('Users');
        $this->addBehavior('Timestamp');
    }

    public function markAsRead(User $user, $timestamp)
    {
        return $this->updateAll(["is_viewed" => true], ["created <" => $timestamp, "user_id" => $user->id]);
    }

}

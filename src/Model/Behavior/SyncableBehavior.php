<?php

namespace App\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Table;
use Cake\ORM\Entity;
use Cake\Model\Behavior\TimestampBehavior;

class SyncableBehavior extends TimestampBehavior {

    public function __construct(Table $table, array $config = []) {
        parent::__construct($table, $config);

        $this->config('events', [
            'Model.beforeSave' => ['created' => 'new'],
            'Lastfm.onUpdate' => ['modified' => 'always']
        ], false);
    }

    public function beforeSave(Event $event, Entity $entity)
    {
        if (!$entity->isNew()) {
            $this->touch($entity, 'Lastfm.onUpdate');
        }
    }
}

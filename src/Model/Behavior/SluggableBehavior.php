<?php

namespace App\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;

class SluggableBehavior extends Behavior {

    protected $_defaultConfig = [
        'field' => 'name',
        'slug' => 'slug',
        'replacement' => '-',
        'contain' => null
    ];

    public function findSlug(Query $query, array $options)
    {
        $config = $this->config();

        $query = $query->where([
            $query->repository()->alias() . "." . $config['slug'] => $options['slug']
        ]);

        if (count($config['contain'])) {
            $query = $query->contain($config['contain']);
        }
        return $query;
    }

    public function beforeSave(Event $event, Entity $entity)
    {
        if ($entity->isNew()) {
            $entity->assignUniqueSlug();
        }
    }
}

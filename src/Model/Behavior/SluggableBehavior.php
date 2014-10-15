<?php

namespace App\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\Utility\Inflector;

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

    public function slug(Entity $entity)
    {
        $config = $this->config();
        $value = $entity->get($config['field']);

        if(empty(trim($value))) {
            throw new Error("Cannot create an entity with no key.");
        }

        $entity->set($config['slug'], strtolower(Inflector::slug($value, $config['replacement'])));
    }

    public function beforeSave(Event $event, Entity $entity)
    {
        if ($entity->isNew()) {
            $this->slug($entity);
        }
    }

}

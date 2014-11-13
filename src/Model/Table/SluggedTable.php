<?php
namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use Exception;

// This is not a behaviour because when referencing with $this, it points to the behaviour class, not the entity
class SluggedTable extends Table {


    public function beforeSave(Event $event, Entity $entity)
    {
        if ($entity->isNew()) {
            $entity->set("slug", $this->generateUniqueSlug($entity));
        }
    }

    public function generateUniqueSlug(Entity $entity)
    {
        // I bet this could be improved. For now, loop until we have a unique slug
        // in the model's table.
        $i = 0;
        $slug = strtolower(Inflector::slug($entity->get('name')));

        while ($this->findBySlug($slug)->count() > 0) {
            if (!preg_match ('/-{1}[0-9]+$/', $slug )) {
                $slug .= '-' . ++$i;
            }
            else {
                $slug = preg_replace ('/[0-9]+$/', ++$i, $slug );
            }
        }

        return $slug;
    }

}

<?php
namespace App\Model\Entity;

use Cake\Core\Configure;
use Cake\Error\Exception;
use Cake\ORM\TableRegistry;

trait ImplicitRelationTrait {

    public function isLinkedToObject()
    {
        return (int)$this->related_model_id > 0;
    }

    public function getLinkedObject()
    {
        $relatedModel = $this->_getRelatedModel($this->type);
        if($relatedModel) {
            return $relatedModel->get((int)$this->related_model_id);
        }
    }

    public function getSupportedTypes()
    {
        // value_in_db => table_object_name
        return [
            "achievement" => "Achievements",
            "follower"    => "Users",
            "account"     => "Users",
            "artist"      => "Artists",
            "review"      => "Tracks",
            "bug"         => "Bugs"
        ];
    }

    private function _isSupportedType($type) {
        return in_array($type, array_keys($this->getSupportedTypes()));
    }


    private function _getRelatedModel($type) {
        if($this->_isSupportedType($this->type)) {
            $types = $this->getSupportedTypes();
            return TableRegistry::get($types[$type]);
        }
    }

}

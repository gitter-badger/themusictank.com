<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Entity\ThumbnailTrait;
use Cake\Auth\DefaultPasswordHasher;

class User extends Entity
{
    use ThumbnailTrait;

    protected $_accessible = ['*' => true];

    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }

    public function isAdmin()
    {
        return $this->role === "admin";
    }
}

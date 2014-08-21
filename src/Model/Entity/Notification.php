<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Entity\ImplicitRelationTrait;

class Notification extends Entity
{
    use ImplicitRelationTrait;

    const TYPE_ACHIEVEMENT = "achievement";
    const TYPE_FOLLOWER    = "follower";
    const TYPE_BUG         = "bug";

}

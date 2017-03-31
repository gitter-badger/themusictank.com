<?php

namespace App\Models\Entities;

use App\Models\Entities\Behavior\Dated;

class Notification
{
    use Dated;

    public $id;
    public $type;
    public $createdAt;
    public $viewed;
    public $associatedObjectId;


    public function isViewed()
    {
        return (bool)$this->viewed;
    }

    public function hasLinkedObject()
    {
        return (int)$this->associatedObjectId > 0;
    }

    public function getLinkedObjectType()
    {
        return "dude";
    }

    public function getCreatedDateForHumans()
    {
        return "dude";
    }

}

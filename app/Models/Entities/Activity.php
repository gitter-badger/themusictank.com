<?php

namespace App\Models\Entities;

use App\Models\Entities\Behavior\Dated;

class Activity
{
    use Dated;

    public $id;
    public $associated_object_id;
    public $associated_object_type;
    public $must_notify;
    public $updated_at;

    public function isViewed()
    {
        return (int)$this->must_notify > 0;
    }

    public function hasLinkedObject()
    {
        return (int)$this->associated_object_id > 0;
    }

    public function getLinkedObject()
    {
        return "dude";
    }

    public function getCreatedDateForHumans()
    {
        return "dude";
    }

}

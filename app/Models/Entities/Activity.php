<?php

namespace App\Models\Entities;

use App\Models\Entities\Behavior\Dated;
use App\Models\Activities;
use App\Models\Restful\ModelParser;

use JsonSerializable;

class Activity implements JsonSerializable
{
    use Dated;

    public $id;
    public $associated_object_id;
    public $associated_object_type;
    public $must_notify;
    public $updated_at;
    public $created_at;
    public $associated_object;
    public $association_title;
    public $association_summary;
    public $association_link;

    public function jsonSerialize()
    {
        return [
            "id" => $this->id,
            "must_notify" => $this->must_notify,
            "associated_object_type" => $this->associated_object_type,
            "associated_object_id" => $this->associated_object_id,
            "associated_object" => $this->associated_object,
            "association_title"   => $this->association_title,
            "association_summary"=> $this->association_summary,
            "association_link"=> $this->association_link,
            "updated_at" => $this->updated_at,
            "created_at" => $this->created_at,
        ];
    }

    public function isViewed()
    {
        return (int)$this->must_notify < 1;
    }

    public function hasLinkedObject()
    {
        return (int)$this->associated_object_id > 0;
    }

    public function getAssociationType()
    {
        return $this->associated_object_type;
    }

    public function getLinkedObject()
    {
        return $this->associated_object;
    }
}

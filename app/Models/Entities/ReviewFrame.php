<?php

namespace App\Models\Entities;

use JsonSerializable;

class ReviewFrame implements JsonSerializable
{
    public $id;
    public $trackId;
    public $profileId;
    public $groove;
    public $second;

    public function jsonSerialize()
    {
        return [
            "id" => $this->id,
            "trackId" => $this->trackId,
            "profileId" => $this->profileId,
            "groove" => $this->groove,
            "position" => $this->position
        ];
    }
}

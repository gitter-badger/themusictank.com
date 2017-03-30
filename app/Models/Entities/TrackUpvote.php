<?php

namespace App\Models\Entities;

use JsonSerializable;

class TrackUpvote implements JsonSerializable
{
    public $id;
    public $trackId;
    public $vote;
    public $profileId;

    public function jsonSerialize()
    {
        return [
            "id" => $this->trackId,
            "vote" => $this->vote
        ];
    }
}

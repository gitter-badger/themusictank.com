<?php

namespace App\Models\Entities;

use JsonSerializable;

class AlbumUpvote implements JsonSerializable
{
    public $id;
    public $albumId;
    public $vote;
    public $profileId;

    public function jsonSerialize()
    {
        return [
            "id" => $this->albumId,
            "vote" => $this->vote
        ];
    }
}

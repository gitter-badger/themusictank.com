<?php

namespace App\Models;

use App\Models\Restful\Model;

class TrackUpvotes extends Model
{
    public $belongsTo = [
        "artist" => "App\Models\Entities\Artist",
        "track" => "App\Models\Entities\Track"
    ];

    public function vote($id, $artistId, $profileId, $type)
    {
        return $this->post("trackupvotes", [
            "json" => [
                "artistId" => $artistId,
                "trackId" => $id,
                "profileId" => $profileId,
                "type" => $type
            ],
        ]);
    }
}

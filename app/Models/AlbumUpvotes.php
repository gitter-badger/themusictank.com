<?php

namespace App\Models;

use App\Models\Restful\Model;

class AlbumUpvotes extends Model
{
    public $belongsTo = [
        "artist" => "App\Models\Entities\Artist",
        "album" => "App\Models\Entities\Album"
    ];

    public function vote($id, $artistId, $profileId, $type)
    {
        return $this->post("albumupvotes", [
            "json" => [
                "artistId" => $artistId,
                "albumId" => $id,
                "profileId" => $profileId,
                "type" => $type
            ],
        ]);
    }
}

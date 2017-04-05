<?php

namespace App\Models;

use App\Models\Restful\Model;
use App\Models\Traits\Upvotable;

class AlbumUpvotes extends Model
{
    use Upvotable;

    public $belongsTo = [
        "artist" => "App\Models\Entities\Artist",
        "album" => "App\Models\Entities\Album"
    ];

    public function vote($id, $profileId, $vote)
    {
        return $this->post("albumupvotes/upsertWithWhere", [
            "json" => [
                "albumId" => (int)$id,
                "profileId" => (int)$profileId,
                "vote" => (int)$vote
            ],
            "query" => [
                "where" => [
                    "profileId" => (int)$profileId,
                    "albumId" => (int)$id
                ],
            ]
        ]);
    }

    public function removeVote($id, $profileId)
    {
        return $this->delete("albumupvotes/deleteByForeign", [
             "json" => [
                    "albumId" =>  (int)$id,
                    "profileId" => (int)$profileId
            ]
        ]);
    }
}

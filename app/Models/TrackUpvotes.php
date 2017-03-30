<?php

namespace App\Models;

use App\Models\Restful\Model;
use App\Models\Traits\Upvotable;

class TrackUpvotes extends Model
{
    use Upvotable;

    public $belongsTo = [
        "artist" => "App\Models\Entities\Artist",
        "track" => "App\Models\Entities\Track"
    ];

    public function vote($id, $profileId, $vote)
    {
        return $this->post("trackupvotes", [
            "json" => [
                "trackId" => (int)$id,
                "profileId" => (int)$profileId,
                "vote" => (int)$vote
            ],
        ]);
    }

    public function removeVote($id, $profileId)
    {
        return $this->delete("trackupvotes/deleteByForeign", [
             "json" => [
                    "trackId" =>  (int)$id,
                    "profileId" => (int)$profileId
            ]
        ]);
    }
}

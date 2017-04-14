<?php

namespace App\Models;

use App\Models\Restful\Model;

class TrackReviews extends Model
{
    public $belongsTo = [
        "profile" => \App\Models\Entities\Profile::class,
        "track" => \App\Models\Entities\Track::class
    ];

    public function global(\App\Models\Entities\Track $track)
    {
        return $this->get("trackReviews", [
            "query" => [
                "filter" => [
                    "where" => [
                        "trackId" =>  $track->id,
                        "profileId" => null
                    ]
                ]
            ]
        ]);
    }
}

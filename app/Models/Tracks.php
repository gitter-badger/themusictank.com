<?php

namespace App\Models;

use App\Models\Restful\Model;

class Tracks extends Model
{
    public $belongsTo = [
        "artist" => \App\Models\Entities\Artist::class,
        "album" => \App\Models\Entities\Album::class
    ];

    public function search($query, $limit = 10)
    {
        return $this->get("tracks", [
            "query" => [
                "filter" => [
                    "include" => ['artist', 'album'],
                    "where" => ["name" => ["regexp" => '^'.$query.'/i']],
                    "limit" => $limit
                ]
            ]
        ]);
    }

    public function fetchCount()
    {
        return $this->get("artists/count");
    }
}

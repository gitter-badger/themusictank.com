<?php

namespace App\Models;

use App\Models\Restful\Model;

class Artists extends Model
{
    public $hasMany = ["albums" => "App\Models\Entities\Album"];


    public function findTopFeatured($artistLimit = 10, $albumLimit = 4)
    {
        return $this->get("artists", [
            "query" => [
                "filter" => [
                    "where" => ["is_featured" =>  true],
                    "include" => [
                        "relation" => 'albums',
                        "scope" => [
                            "limit" => $albumLimit
                        ]
                    ],
                    "limit" => $artistLimit
                ]
            ]
        ]);
    }

    public function findBySlug($slug)
    {
        return $this->first("artists", [
            "query" => [
                "filter" => [
                    "where" => ["slug" =>  $slug],
                    "include" => "albums"
                ]
            ]
        ]);
    }

}

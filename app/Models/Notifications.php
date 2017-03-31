<?php

namespace App\Models;

use App\Models\Restful\Model;

class Notifications extends Model
{
    public $hasMany = [
        "profiles" => \App\Models\Entities\Profile::class,
        "achievements" => \App\Models\Entities\Achievement::class
    ];

    public function findRecent($profileId, $limit = 20)
    {
        return $this->get("notifications", [
            "query" => [
                "filter" => [
                    "where" => ["profileId" =>  $profileId],
                    "limit" => $limit
                ]
            ]
        ]);
    }
}

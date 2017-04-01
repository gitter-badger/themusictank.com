<?php

namespace App\Models;

use App\Models\Restful\Model;

class Activities extends Model
{
    public function findSince($dateTime, $profileId, $limit = 6)
    {
        return $this->get("activities", [
            "query" => [
                "filter" => [
                    "where" => [
                        "profileid" => $profileId,
                        "created_at" => ["gt" => $dateTime],
                        "must_notify" => 1
                    ],
                    "limit" => $limit,
                    "order" => "created_at DESC"
                ]
            ]
        ]);
    }
    
    public function findRecent($profileId, $limit = 20)
    {
        return $this->get("activities", [
            "query" => [
                "filter" => [
                    "profileid" => $profileId,
                    "limit" => $limit,
                    "order" => "created_at DESC"
                ]
            ]
        ]);
    }
}

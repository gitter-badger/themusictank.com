<?php

namespace App\Models;

use App\Models\Restful\Model;
use App\Models\Traits\Upvotable;

class ApiRequests extends Model
{
    public $belongsTo = [
        "profile" => \App\Models\Entities\Profile::class
    ];

    public function fetch($limit = 100)
    {
        return $this->get("apirequests", [
            "query" => [
                "filter" => [
                    "limit" => $limit,
                    "include" => ['profile'],
                    "order" => "created_at DESC"
                ]
            ]
        ]);
    }

}

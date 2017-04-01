<?php

namespace App\Models;

use App\Models\Restful\Model;
use App\Exceptions\AuthFailedException;

class Profiles extends Model
{
    public $hasMany = [
        "trackUpvotes" => \App\Models\Entities\TrackUpvote::class,
        "albumUpvotes" => \App\Models\Entities\AlbumUpvote::class,
        "activities" => \App\Models\Entities\Activity::class,
    ];

    public function findById($id)
    {
        return $this->get(sprintf("profiles/%d", $id));
    }

    /**
     * Essentially the same as findById, but pulls
     * additional data because this is intented to kickoff
     * a user session.
     */
    public function findByIdForLogin($id)
    {
        return $this->get(sprintf("profiles/%d", $id), [
             "query" => [
                "filter" => [
                    "include" => [
                        'trackUpvotes',
                        'albumUpvotes',
                        'activities'
                    ]
                ]
            ]
        ]);
    }

    public function findBySlug($slug)
    {
        return $this->get(sprintf("profiles/%s", $slug));
    }
}

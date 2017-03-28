<?php

namespace App\Models;

use App\Models\Restful\Model;
use App\Exceptions\AuthFailedException;

class Profiles extends Model
{
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
                        'albumUpvotes'
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

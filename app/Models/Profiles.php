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

    public function findBySlug($slug)
    {
        return $this->get(sprintf("profiles/%s", $slug));
    }
}

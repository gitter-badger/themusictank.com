<?php

namespace App\Models;

use App\Models\Restful\Model;
use App\Exceptions\AuthFailedException;

class Profiles extends Model
{
    public function findProfileById($id)
    {
        return $this->get(sprintf("profiles/%d", $id));
    }

}

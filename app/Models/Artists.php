<?php

namespace App\Models;

use App\Models\Restful\Model;

class Artists extends Model
{
    public $hasMany = ["albums" => "App\Models\Entities\Album"];
}

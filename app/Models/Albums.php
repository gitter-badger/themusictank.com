<?php

namespace App\Models;

use App\Models\Restful\Model;

class Albums extends Model
{
    public $hasMany = ["tracks" => "App\Models\Entities\Track"];
    public $belongsTo = ["artist" => "App\Models\Entities\Artist"];
}

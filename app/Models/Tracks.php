<?php

namespace App\Models;

use App\Models\Restful\Model;

class Tracks extends Model
{
    public $belongsTo = [
        "artist" => "App\Models\Entities\Artist",
        "album" => "App\Models\Entities\Album"
    ];
}

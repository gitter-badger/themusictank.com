<?php

namespace App\Models;

class Tracks extends RestModel
{
    protected $belongsTo = [
        "artist" => "App\Models\Entities\Artist",
        "album" => "App\Models\Entities\Album"
    ];
}

<?php

namespace App\Models;

use App\Models\Restful\Model;

class Albums extends Model
{
    public $hasMany = [
        "tracks" => \App\Models\Entities\Track::class
    ];

    public $belongsTo = [
        "artist" => \App\Models\Entities\Artist::class
    ];
}

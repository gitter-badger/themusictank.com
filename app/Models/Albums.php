<?php

namespace App\Models;

class Albums extends RestModel
{
    protected $hasMany = ["tracks" => "App\Models\Entities\Track"];
    protected $belongsTo = ["artist" => "App\Models\Entities\Artist"];
}

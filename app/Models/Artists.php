<?php

namespace App\Models;

class Artists extends RestModel
{
    protected $hasMany = ["albums" => "App\Models\Entities\Album"];
}

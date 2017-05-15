<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;

class SluggedModel extends AppModel
{
    use Sluggable;

    public function sluggable()
    {
        return ['slug' => ['source' => 'name']];
    }
}

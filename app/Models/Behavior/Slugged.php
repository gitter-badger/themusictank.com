<?php

namespace App\Models\Behavior;

use Cviebrock\EloquentSluggable\Sluggable;

trait Slugged
{
    use Sluggable;

    public function sluggable()
    {
        return ['slug' => ['source' => 'name']];
    }
}

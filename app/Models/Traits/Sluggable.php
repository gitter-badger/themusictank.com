<?php

namespace App\Models\Traits;

trait Sluggable
{
    public static function slug($slug)
    {
        return $this->where("slug", $slug);
    }
}

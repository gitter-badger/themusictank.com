<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use Entities\Behavior\Thumbnailed,
        Entities\Behavior\Dated,
        Traits\Sluggable,
        Traits\Searchable;

    public function albums()
    {
        return $this->hasMany(\App\Models\Album::class);
    }
}

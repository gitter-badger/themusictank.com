<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use Entities\Behavior\Thumbnailed,
        Entities\Behavior\Dated,
        Traits\Sluggable,
        Traits\Searchable;

    public function artist() {
        return $this->belongsTo(\App\Models\Artist::class);
    }

    public function tracks() {
        return $this->hasMany(\App\Models\Track::class);
    }
}

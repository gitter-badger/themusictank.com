<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use Entities\Behavior\Thumbnailed,
        Entities\Behavior\Dated,
        Traits\Sluggable,
        Traits\Searchable;

    public function album() {
        return $this->belongsTo(\App\Models\Album::class);
    }

    public function artist() {
        return $this->belongsTo(\App\Models\Artist::class);
    }
}

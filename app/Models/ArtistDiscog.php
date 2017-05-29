<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArtistDiscog extends Model
{
    public function artist()
    {
        return $this->hasOne(\App\Models\Artist::class);
    }
}

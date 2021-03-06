<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArtistDiscog extends Model
{
    protected $fillable = [
        "discog_id",
        "state"
    ];

    public function artist()
    {
        return $this->belongsTo(\App\Models\Artist::class);
    }
}

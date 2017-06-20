<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlbumDiscog extends Model
{
    protected $fillable = [
        "discog_id",
        "state"
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}

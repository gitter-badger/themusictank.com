<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackDiscog extends Model
{
    protected $fillable = [
        "discog_id",
        "state"
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }
}

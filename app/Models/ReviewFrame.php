<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReviewFrame extends Model
{
    protected $fillable = [
        "id",
        "track_id",
        "user_id",
        "groove",
        "position"
    ];

    public function track() {
        return $this->belongsTo(\App\Models\Track::class);
    }

    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }
}

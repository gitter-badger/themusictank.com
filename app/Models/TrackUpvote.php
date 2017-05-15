<?php

namespace App\Models;

class TrackUpvote extends AppModel
{
    protected $fillable = [
        'track_id',
        'vote',
        'user_id'
    ];

    public function track() {
        return $this->belongsTo(\App\Models\Track::class);
    }

    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }
}

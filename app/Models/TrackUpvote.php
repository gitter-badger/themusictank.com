<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackUpvote extends Model
{
    use Behavior\Dated;

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

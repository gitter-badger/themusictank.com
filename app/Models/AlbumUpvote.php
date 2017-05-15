<?php

namespace App\Models;

class AlbumUpvote extends AppModel
{
    protected $fillable = [
        'album_id',
        'vote',
        'user_id'
    ];

    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function album() {
        return $this->belongsTo(\App\Models\Album::class);
    }
}

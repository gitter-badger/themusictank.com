<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlbumUpvote extends Model
{
    use Behavior\Dated;

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

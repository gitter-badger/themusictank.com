<?php

namespace App\Models;

class Album extends AppModel
{
    use Behavior\Thumbnailed,
        Behavior\Slugged,
        Behavior\Searchable;

    protected $fillable = [
        'artist_id',
        'name',
        'slug',
        'gid',
        'year',
        'month',
        'day',
        'thumbnail',
    ];

    public function artist() {
        return $this->belongsTo(\App\Models\Artist::class);
    }

    public function tracks() {
        return $this->hasMany(\App\Models\Track::class);
    }
}

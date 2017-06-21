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
        'hex',
        'year',
        'month',
        'day',
        'thumbnail',
    ];

    public function artist() {
        return $this->belongsTo(Artist::class);
    }

    public function tracks() {
        return $this->hasMany(Track::class)->orderBy("position", "ASC");
    }

    public function discog()
    {
        return $this->hasOne(AlbumDiscog::class)->first();
    }
}

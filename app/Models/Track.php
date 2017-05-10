<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Track extends Model
{
    use Entities\Behavior\Thumbnailed,
        Entities\Behavior\Dated,
        Sluggable;

    protected $fillable = [
        'artist_id',
        'album_id',
        'name',
        'slug',
        'gid',
        'youtube_key',
        'position',
        'length'
    ];

    public function album() {
        return $this->belongsTo(\App\Models\Album::class);
    }

    public function artist() {
        return $this->belongsTo(\App\Models\Artist::class);
    }

    public function sluggable()
    {
        return ['slug' => ['source' => 'name']];
    }

    public function scopeSearch($query, $criteria)
    {
        return $query->where("name", 'ilike', "%$criteria%");
    }

    public function scopeNext($query, \App\Models\Track $pointer)
    {
        return $query
            ->wherePosition($track->position + 1)
            ->andWhereArtistId($track->artist->id)
            ->andWhereAlbumId($track->album->id);
    }
}

<?php

namespace App\Models;

class Track extends AppModel
{
    use Behavior\Thumbnailed,
        Behavior\Slugged,
        Behavior\Searchable;

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

    public function scopeNext($query)
    {
        return $query
            ->wherePosition($this->position + 1)
            ->whereArtistId($this->artist->id)
            ->whereAlbumId($this->album->id);
    }

    public function scopePrevious($query)
    {
        return $query
            ->wherePosition($this->position - 1)
            ->whereArtistId($this->artist->id)
            ->whereAlbumId($this->album->id);
    }
}

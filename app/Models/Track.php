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
        'position_int',
        'length'
    ];

    public function album() {
        return $this->belongsTo(Album::class);
    }

    public function artist() {
        return $this->belongsTo(Artist::class);
    }

    public function discog()
    {
        return $this->hasOne(TrackDiscog::class)->first();
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

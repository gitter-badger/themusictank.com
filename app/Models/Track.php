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

    public function fill(array $attributes)
    {
        parent::fill($attributes);

        // Discogs supplies position as a string in which
        // it sorts how albums on multiple cds are separated.
        $this->position_int = $this->isPartOfMany() ?
            ($this->getSupportIndex() * 100) + $this->getRealPosition() :
            (int)$this->position;

        return $this;
    }

    public function scopeNext($query)
    {
        return $query
            ->where('position_int', '>', $this->position_int)
            ->whereArtistId($this->artist->id)
            ->whereAlbumId($this->album->id)
            ->orderBy('position_int');
    }

    public function scopePrevious($query)
    {
        return $query
            ->where('position_int', '<', $this->position_int)
            ->whereArtistId($this->artist->id)
            ->whereAlbumId($this->album->id)
            ->orderBy('position_int', 'DESC');
    }

    public function isSupportLabel()
    {
        return is_null($this->position);
    }

    public function getSupportIndex()
    {
        if ($this->isPartOfMany() &&  preg_match('/^(\d+)\-\d+$/', $this->position, $matches)) {
            return (int)$matches[1];
        }
    }

    public function isPartOfMany()
    {
        return preg_match('/^(\d+)\-(\d+)$/', $this->position);
    }

    public function getRealPosition()
    {
        if ($this->isPartOfMany() && preg_match('/^\d+\-(\d+)$/', $this->position, $matches)) {
            return (int)$matches[1];
        }

        return (int)$this->position_int;
    }
}

<?php

namespace App\Models;

class Artist extends AppModel
{
    use Behavior\Thumbnailed,
        Behavior\Slugged,
        Behavior\Searchable;

    protected $fillable = [
        'name',
        'slug',
        'hex',
        'thumbnail',
        'is_featured',
    ];

    public function albums()
    {
        return $this->hasMany(Album::class)->orderBy("year", "DESC");
    }

    public function discog()
    {
        return $this->hasOne(ArtistDiscog::class)->first();
    }
}

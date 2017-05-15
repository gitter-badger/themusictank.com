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
        'gid',
        'thumbnail',
        'is_featured',
    ];

    public function albums()
    {
        return $this->hasMany(\App\Models\Album::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Artist extends Model
{
    use Behavior\Thumbnailed,
        Behavior\Dated,
        Sluggable;

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

    public function sluggable()
    {
        return ['slug' => ['source' => 'name']];
    }

    public function scopeSearch($query, $criteria)
    {
        return $query->where("name", 'ilike', "%$criteria%");
    }
}

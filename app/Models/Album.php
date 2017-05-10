<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Album extends Model
{
    use Behavior\Thumbnailed,
        Behavior\Dated,
        Sluggable;

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

    public function sluggable()
    {
        return ['slug' => ['source' => 'name']];
    }

    public function scopeSearch($query, $criteria)
    {
        return $query->where("name", 'ilike', "%$criteria%");
    }
}

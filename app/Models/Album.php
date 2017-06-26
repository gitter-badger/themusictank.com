<?php

namespace App\Models;

use DB;

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

    public function globalScore()
    {
        return DB::table('track_reviews')
            ->whereIn('track_id', $this->tracks->pluck('id'))
            ->avg('avg_groove');
    }

    public function subsScore(User $user)
    {
        return DB::table('track_reviews')
            ->whereIn('track_id', $this->tracks->pluck('id'))
            ->where('user_id', $user->subscriptions->pluck('id'))
            ->avg('avg_groove');
    }
}

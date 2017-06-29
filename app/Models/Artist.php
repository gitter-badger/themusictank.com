<?php

namespace App\Models;

use DB;

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

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function discog()
    {
        return $this->hasOne(ArtistDiscog::class)->first();
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
            ->whereIn('user_id', $user->subscriptions->pluck('id'))
            ->avg('avg_groove');
    }

    public function score(User $user)
    {
        return DB::table('track_reviews')
            ->whereIn('track_id', $this->tracks->pluck('id'))
            ->where('user_id', $user->id)
            ->avg('avg_groove');
    }
}

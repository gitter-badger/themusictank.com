<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Track;
use App\Models\User;

class TrackReview extends Model
{
    use \App\Models\Behavior\Dated;

    protected $fillable = [
        "id",
        "track_id",
        "user_id",
        'avg_groove',
        'high_avg_groove',
        'low_avg_groove',
        "position"
    ];

    public function track()
    {
        return $this->belongsTo(\App\Models\Track::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function scopeGlobal($query, Track $track)
    {
        return $query
            ->whereTrackId($track->id)
            ->whereUserId(null);
    }

    public function scopeComponentFields($query)
    {
        return $query->select([
            'position',
            'avg_groove',
            'high_avg_groove',
            'low_avg_groove'
        ]);
    }
}

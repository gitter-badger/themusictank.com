<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Observers\UserObserver;

class User extends Authenticatable
{
    use Notifiable,
        Sluggable,
        Behavior\Dated;

    protected $fillable = [
        'name',
        'email',
        'thumbnail',
        'slug',
        'password'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function boot()
    {
        User::observe(new UserObserver);
    }

    public function trackReviews()
    {
        return $this->hasMany(\App\Models\TrackReview::class);
    }

    public function trackUpvotes()
    {
        return $this->hasMany(\App\Models\TrackUpvote::class);
    }

    public function albumUpvotes()
    {
        return $this->hasMany(\App\Models\AlbumUpvote::class);
    }

    public function activities()
    {
        return $this->hasMany(\App\Models\Activity::class);
    }

    public function userAchievements()
    {
        return $this->hasMany(\App\Models\UserAchievements::class);
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

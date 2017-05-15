<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Observers\UserObserver;

class User extends Authenticatable
{
    use Notifiable,
        Behavior\Slugged,
        Behavior\Searchable,
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

    protected $rules = [
        'name' => 'required',
        'email' => 'required',
        'password' => 'required',
        'password_confirm' => 'same:password',
        'slug' => 'required',
    ];

    public static function boot()
    {
        parent::boot();
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
}

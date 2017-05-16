<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Models\Achievements\WelcomeToTmt;
use App\Services\AchievementService;

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

    public static function boot()
    {
        parent::boot();

        static::created(function($model)
        {
            AchievementService::grant(new WelcomeToTmt(), $user);
        });
    }

    public function socialAccounts()
    {
        return $this->hasMany(\App\Models\SocialAccount::class);
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
        return $this->hasMany(\App\Models\Activity::class)->orderBy("created_at", "DESC");
    }

    public function userAchievements()
    {
        return $this->hasMany(\App\Models\UserAchievements::class)->orderBy("created_at", "DESC");
    }

    public function subscriptions()
    {
        return $this->hasMany(\App\Models\UserSubscription::class);
    }
}

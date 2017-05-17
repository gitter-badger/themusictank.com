<?php

namespace App\Models;

use App\Services\AchievementService;
use Illuminate\Support\Facades\DB;

class UserAchievement extends AppModel
{
    protected $fillable = [
        'achievement_id',
        'user_id'
    ];

    protected $appends = ['achievement'];

    public function getAchievementAttribute() {
        return AchievementService::findById($this->achievement_id);
    }

    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function scopeRare($query)
    {
        return $query->select(['achievement_id', DB::raw('count(user_id) as total')])
            ->groupBy('achievement_id')
            ->orderBy('total', 'asc');
    }

}

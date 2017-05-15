<?php

namespace App\Models;

use App\Service\AchievementService;

class UserAchievement extends AppModel
{
    protected $fillable = [
        'achievement_id',
        'user_id'
    ];

    public function archievement() {
        return AchievementService::findById($this->achievement_id);
    }

    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }
}

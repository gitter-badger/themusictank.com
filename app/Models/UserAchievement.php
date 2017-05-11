<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAchievement extends Model
{
    protected $fillable = [
        'achievement_id',
        'user_id'
    ];

    public function archievement() {
        // return $this->belongsTo(\App\Models\Achievement::class);
    }

    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }
}

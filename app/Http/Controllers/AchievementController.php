<?php

namespace App\Http\Controllers;

use App\Models\UserAchievement;
use App\Models\User;
use App\Services\AchievementService;

class AchievementController extends Controller
{
    public function index()
    {
        $userCount = User::count();
        $rareAchievements = [];
        foreach (UserAchievement::rare()->take(10)->get() as $info) {
            $achivement = AchievementService::findById($info->achievement_id);
            $achivement->pct = ceil((int)$info->total / $userCount * 100);
            $rareAchievements[] = $achivement;
        }

        if ($this->hasActiveSession()) {
            $userAchievements = UserAchievement::whereUserId($this->authUserId())
                ->orderBy("created_at", "DESC")
                ->take(10)
                ->get();
        }

        return view('achievements.index', compact('rareAchievements', 'userAchievements', 'userCount', 'achievementsCounts'));
    }

    public function all()
    {
        $achievements = AchievementService::collect();

        return view('achievements.all', compact('achievements'));
    }

    public function show($slug)
    {
        $achievement = AchievementService::findBySlug($slug);
        if (is_null($achievement)) {
            return abort(404);
        }

        $rareness = UserAchievement::rare()->whereAchievementId($achievement->id)->first();
        $popularity = ceil($rareness->total / User::count() * 100);

        if ($this->hasActiveSession()) {
            $authUserHasIt = UserAchievement::whereUserId($this->authUserId())
                ->whereAchievementId($achievement->id)
                ->first();
        }

        return view('achievements.show', compact('achievement', 'authUserHasIt', 'popularity', 'userCount'));
    }

}

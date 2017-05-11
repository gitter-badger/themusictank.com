<?php

namespace App\Services;

use App\Models\Achievements\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use Exception;

class AchievementService
{
    private static $modelFiles = null;

    public static function grant(Achievement $achievement, User $user, $additional = [])
    {
        if (!self::exists($achievement, $user) && $achievement->applies($user, $additional)) {
            $userAchievement = new UserAchievement();
            $userAchievement->user_id = $user->id;
            $userAchievement->achievement_id = $achievement->id;
            return $userAchievement->save() && ActivityService::achievement($user, $achievement);
        }

        return false;
    }

    public static function exists(Achievement $achievement, User $user)
    {
        return UserAchievement::whereAchievementId($achievement->id)
            ->whereUserId($user->id)
            ->count() > 0;
    }

    public static function find($slug)
    {
        $classname = "\\App\\Models\\Achievements\\" . $slug;
        if ($slug != "Achievement" && $slug != "IAchievement"  && class_exists($classname)) {
            return new $classname();
        }

        throw new Exception(sprintf("%s is not a valid achievement identifier.", $slug));
    }

    public static function findById($id)
    {
        $files = array_diff(scandir(app_path('Models\\Achievements')), array('.', '..', 'Achievement.php'));

        foreach ($files as $file) {
            $achievement = self::find(basename($file, '.php'));
            if ($achievement->id === $id) {
                return $achievement;
            }
        }

        throw new Exception(sprintf("%s is not a valid achievement id.", $id));
    }
}

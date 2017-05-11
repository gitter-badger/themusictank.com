<?php

namespace App\Services;

use App\Models\Achievements\Achievement;
use App\Models\User;
use App\Models\Track;
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

    public static function collect() {
        $files = array_diff(scandir(app_path('Models\\Achievements')), array('.', '..', 'Achievement.php'));
        return array_map(function($file) {
            return self::find(basename($file, '.php'));
        }, $files);
    }

    public static function collectForTrack(Track $track)
    {
        return array_filter(self::collect(), function ($achievement) use ($track) {
            return in_array($track->id, $achievement->trackIdsTriggers);
        });
    }

    public static function collectForAlbum(Album $album)
    {
        return array_filter(self::collect(), function ($achievement) use ($album) {
            return in_array($album->id, $achievement->albumIdsTriggers);
        });
    }

    public static function collectForArtist(Artist $artist)
    {
        return array_filter(self::collect(), function ($achievement) use ($artist) {
            return in_array($artist->id, $achievement->artistIdsTriggers);
        });
    }

    public static function find($slug)
    {
        if ($slug != "Achievement") {
            $classname = "\\App\\Models\\Achievements\\" . $slug;
            if (class_exists($classname)) {
                return new $classname();
            }
        }

        throw new Exception(sprintf("%s is not a valid achievement identifier.", $slug));
    }

    public static function findById($id)
    {
        foreach (self::collect() as $achievement) {
            if ($achievement->id === $id) {
                return $achievement;
            }
        }

        throw new Exception(sprintf("%s is not a valid achievement id.", $id));
    }

}

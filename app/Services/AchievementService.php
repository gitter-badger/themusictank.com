<?php

namespace App\Services;

use App\Models\Achievements\Achievement;
use App\Models\User;
use App\Models\Track;
use App\Models\Album;
use App\Models\Artist;
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
        // If the crawl was never made during the lifetime of
        // the object, scan the files in the achievement directory and
        // preload the models for subsequent use.
        if (is_null(self::$modelFiles)) {
            $files = array_diff(scandir(app_path('Models\\Achievements')), ['.', '..', 'Achievement.php']);
            self::$modelFiles = array_map(function($file) {
                $filename = basename($file, '.php');
                return self::find($filename);
            }, $files);

            // Reset index
            self::$modelFiles = array_values(self::$modelFiles);
        }

        return self::$modelFiles;
    }

    public static function collectForTrack(Track $track)
    {
        return array_filter(self::collect(), function ($achievement) use ($track) {
            return in_array($track->id, $achievement->trackTriggers());
        });
    }

    public static function collectForAlbum(Album $album)
    {
        return array_filter(self::collect(), function ($achievement) use ($album) {
            return in_array($album->id, $achievement->albumTriggers());
        });
    }

    public static function collectForArtist(Artist $artist)
    {
        return array_filter(self::collect(), function ($achievement) use ($artist) {
            return in_array($artist->id, $achievement->artistTriggers());
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
    }

    public static function findById($id)
    {
        foreach (self::collect() as $achievement) {
            if ($achievement->id === (int)$id) {
                return $achievement;
            }
        }
    }
}

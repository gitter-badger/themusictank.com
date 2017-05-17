<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use App\Models\Achievements\Achievement;

class ActivityService
{
    const TYPE_ACHIEVEMENT = 1;
    const TYPE_USER = 2;

    public static function achievement(User $user, Achievement $achievement)
    {
        $activity = new Activity();
        $activity->user_id = $user->id;
        $activity->associated_object_id = $achievement->id;
        $activity->associated_object_type = self::TYPE_ACHIEVEMENT;
        $activity->must_notify = true;
        return $activity->save();
    }

    public static function user(User $user, User $follows)
    {
        $activity = new Activity();
        $activity->user_id = $user->id;
        $activity->associated_object_id = $follows->id;
        $activity->associated_object_type = self::TYPE_USER;
        $activity->must_notify = true;
        return $activity->save();
    }

    public static function loadAssociation(Activity $activity)
    {
        $id = $activity->associated_object_id;
        switch ($activity->associated_object_type) {
            case self::TYPE_ACHIEVEMENT   : return AchievementService::findById($id);
            case self::TYPE_USER          : return User::find($id);
        }
    }

    public static function getAssociationKey(Activity $activity)
    {
        switch ($activity->associated_object_type) {
            case self::TYPE_ACHIEVEMENT   : return "achievement";
            case self::TYPE_USER          : return "user";
        }
    }

}

<?php
App::uses('CakeEventListener', 'Event');

class ActivityListener implements CakeEventListener {

    public function implementedEvents()
    {
        return array(            
            'Model.UserFollowers.onSubscription'    => 'userFollowers_onSubscription',
            'Model.UserAchievements.onCreate'       => 'userAchievements_onCreate'
        );
    }

    public static function userFollowers_onSubscription($event)
    {             
        App::uses('UserActivity', 'Model');
        $activity = new UserActivity();
        $activity->add($event->data["User"]["id"], "SUBSCRIBED_TO", $event->data["UserFollower"]["id"]);
    }

    public static function userAchievements_onCreate($event)
    {             
        App::uses('UserAchievements', 'Model');
        $activity = new UserAchievements();
        $activity->add($event->data["User"]["id"], "ACHIEVEMENT_UNLOCKED", $event->data["Achievement"]["id"]);
    }
        
}
<?php
App::uses('CakeEventListener', 'Event');
App::uses('UserActivity', 'Model');
        
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
        $activity = new UserActivity();
        $activity->add($event->data["User"]["id"], UserActivity::TYPE_FOLLOWER, $event->data["UserFollower"]["id"]);
    }

    public static function userAchievements_onCreate($event)
    {
        $activity = new UserActivity();
        $activity->add($event->data["User"]["id"], UserActivity::TYPE_ACHIEVEMENT, $event->data["Achievement"]["id"]);
    }
        
}
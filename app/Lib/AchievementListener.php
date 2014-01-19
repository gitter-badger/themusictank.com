<?php
App::uses('CakeEventListener', 'Event');

class AchievementListener implements CakeEventListener {

    public function implementedEvents()
    {
        return array(            
            'Model.Artist.onCreate' => 'artist_onCreate',
            'Model.User.onCreate'   => 'user_onCreate'
        );
    }

    public static function user_onCreate($event)
    {     
        $user = new User();
        $user->reward($event->data["User"]["id"], "NEW_USER");
    }
    
    public static function artist_onCreate($event)
    {
        $user = new User();
        $user->reward(AuthComponent::user('id'), "NEW_ARTIST");
    }
    
}
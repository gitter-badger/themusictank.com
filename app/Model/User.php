<?php

App::uses('AuthComponent', 'Controller/Component');
App::uses('CakeSession', 'Model/Datasource');
App::uses('UserActivity', 'Model');
App::uses('Notifications', 'Model');

class User extends AppModel
{	
	public $name    = 'User';    
	public $hasOne  = array('RdioUser', 'FacebookUser');
    public $hasMany = array(
        'UserAchievements', 'Notifications', 'UserFollowers', 
        'UserAlbumReviewSnapshot', 'UserTrackReviewSnapshot', 
        'SubscribersAlbumReviewSnapshot', 'SubscribersTrackReviewSnapshot'
    );
	public $validate = array(
		'username' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'A email is required'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => "This email has already been registered."
			),
			'isemail' => array(
				'rule' => 'email',
				'message' => 'A valid email is required'
			)
		),
		'password' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'A password is required'
			),
			'length' => array(
				'rule' => array('between', 8, 250),
				'message' => 'A password must have at least 8 characters.',
				'allowEmpty' => true
			)
		)
	);
    
    
    const PLAYER_RDIO   = "rdio";
    const PLAYER_MP3    = "mp3";
    
    
    public static function getPreferredPlayer($userdata)
    {                   
        if((int)$userdata["preferred_player_api"] === 1 && CakeSession::read('Player.Rdio'))
        {
            return self::PLAYER_RDIO;
        }
        
        return self::PLAYER_MP3;
    }
        
    public static function getFullName($userdata)
    {
        return $userdata["firstname"] . " " . $userdata['lastname'];
    }
    
    public function beforeSave($options = array())
    {
        if (isset($this->data[$this->alias]['password']))
        {
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
        }
        
        $this->checkSlug(array('firstname', 'lastname'));        
        return true;
    }
    
    public function afterSave($created, $options = array())
    {
        if($created)
        {
            $userId = $this->getData("User.id");
            $this->reward($userId, UserActivity::TYPE_NEW_ACCOUNT);
        }
    }
        
    public function reward($userId, $key)
    {       
        $achievement = $this->UserAchievements->Achievement->findByKey($key);        
        if($achievement)
        {   
            $achievementId    = $achievement["Achievement"]["id"];
            $achievementName  = $achievement["Achievement"]["name"];
            if($this->UserAchievements->notObtained($userId, $achievementId))
            {                
                if($this->UserAchievements->grant($userId, $achievementId))
                {
                    $msg = sprintf(__("Achievement unlocked: \"%s\"!"), $achievementName);
                    return $this->notify($userId, Notifications::TYPE_ACHIEVEMENT, $msg);
                }
            }
        }
        return false;
    }
 
    public function notify($userId, $type, $title, $id = null)
    {
        $this->Notifications->create();
        return $this->Notifications->save(array(
            "type"      => $type,
            "title"     => $title,
            "user_id"   => $userId,
            "related_model_id" => $id
        ));
    }

    public function getSubscribers($userId)
    {
        $idList = $this->UserFollowers->getSubscriptions($userId, true);
        return $this->find('all', array(
            'conditions' => array("User.id" => $idList),
            'fields' => array("User.*")
        ));
    }

    public function getFollowers($userId)
    {
        $idList = $this->UserFollowers->getFollowers($userId, true); 
        return $this->find('all', array(
            'conditions' => array("User.id" => $idList),
            'fields' => array("User.*")
        ));
    }

    public function getCommonSubscriberReview($userId, $trackId)
    {
        $idList     = $this->UserFollowers->getSubscriptions($userId, true);
        $filtered   = $this->SubscribersTrackReviewSnapshot->getUserIdsWhoReviewed($trackId, $idList);
                
        return $this->find("all", array("conditions" => array("User.id" => $filtered), "fields" => array("User.*")));
    }
    
    public function getReviewUserSummary($trackId)
    {
        $filtered = $this->SubscribersTrackReviewSnapshot->getUserIdsWhoReviewed($trackId);
        return $this->find("all", array("conditions" => array("User.id" => $filtered), "fields" => array("User.*")));
    }
    
    public function getUncachedSnapshot($userId)
    {            
        $this->data["User"] = array("id" => $userId);
        $this->UserTrackReviewSnapshot->data = $this->data;
        return $this->UserTrackReviewSnapshot->temporarySnapshot();
    }
    
    
}
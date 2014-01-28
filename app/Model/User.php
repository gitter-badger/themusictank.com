<?php

App::uses('AuthComponent', 'Controller/Component');
App::uses('CakeSession', 'Model/Datasource');

class User extends AppModel
{	
	public $name    = 'User';    
	public $hasOne  = array('RdioUser', 'FacebookUser');
    public $hasMany = array('UserAchievements', 'Notifications', 'UserFollowers', 'UserTrackReviewSnapshot');
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
    
    public static function getPreferredPlayer($userdata)
    {                   
        if((int)$userdata["preferred_player_api"] === 1 && CakeSession::read('Player.Rdio'))
        {
            return "rdio";
        }
        
        return "mp3";
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
            $this->dispatchEvent('onCreate');
        }
    }
        
    public function reward($userId, $key)
    {       
        $achievement = $this->UserAchievements->Achievement->findByKey($key);
        $achievementId = $achievement["Achievement"]["id"];
        
        if($achievement && $this->UserAchievements->checkIfApplies($userId, $achievementId))
        {   
            if($this->UserAchievements->grant($userId, $achievementId))
            {
                $this->notify($userId, "achievement", __("Achivement unlocked: ") . " " . $achievement["Achievement"]["name"], $achievement["Achievement"]["id"]);
            }
        }
    }
 
    public function notify($userId, $type, $title, $id = null)
    {
        return $this->Notifications->save(array(
            "type"      => $type,
            "title"     => $title,
            "user_id"   => $userId,
            "related_model_id" => $id
        ));
    }

    public function getSubscribers($userId)
    {
        $idList = array_values($this->UserFollowers->getSubscriptions($userId));
        return $this->find('all', array(
            'conditions' => array("User.id" => $idList),
            'fields' => array("User.*")
        ));
    }

    public function getFollowers($userId)
    {
        $idList = array_values($this->UserFollowers->getFollowers($userId)); 
        return $this->find('all', array(
            'conditions' => array("User.id" => $idList),
            'fields' => array("User.*")
        ));
    }

}
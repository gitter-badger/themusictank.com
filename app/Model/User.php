<?php

App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel
{	
	public $name    = 'User';    
	public $hasOne  = array('RdioUser', 'FacebookUser');
    public $hasMany = array('UserAchievements', 'Notifications', 'UserFollowers');
    //public $hasAndBelongsToMany = array('UserFollower');
	public $validate = array(
		'username' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'A username is required'
			),
			'unique' => array(
				'rule' => array("checkUnique", array('username')),
				'message' => "This username has already been registered."
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
        
    public function beforeSave($options = array())
    {
        if (isset($this->data[$this->alias]['password']))
        {
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
        }
        
        $this->checkSlug(array('firstname', 'email'));        
        return true;
    }
    
    public function afterSave($created, $options = array())
    {
        if($created)
        {
            $this->dispatchEvent('onCreate');
        }
    }
    
    public function getRdioUserFromUserId($userId)
    {        
        return $this->RdioUser->find("first", array(
            "conditions"    => array("RdioUser.user_id" => $userId),
            "fields"        => array("RdioUser.id", "RdioUser.lastsync")
        ));        
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
    
    
}
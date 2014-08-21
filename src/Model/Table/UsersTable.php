<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class UsersTable extends Table {

    public function initialize(array $config)
    {
        $this->hasOne('FacebookUsers',  ['propertyName' => 'facebook']);

        $this->hasMany('UserAchievements', ['propertyName' => 'achievements']);
        $this->hasMany('Notifications', ['propertyName' => 'notifications']);
        $this->hasMany('UserFollowers', ['propertyName' => 'followers']);
    }

    public function validationDefault(Validator $validator)
    {
        $validator->allowEmpty('username')
        //$validator->validatePresence('username')
            ->add('username', ['unique' => ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'This e-mail already exists']])
            ->add('username', ['validFormat' => ['rule' => 'email', 'message' => 'E-mail must be valid']])
            ->add('password', ['length' => [ 'rule' => ['minLength', 3], 'message' => 'Emails need to be at least 3 characters long']])
            ->add('password', ['length' => [ 'rule' => ['maxLength', 255], 'message' => 'Emails need to be at most 255 characters long']]);

        $validator->allowEmpty('password')
        //$validator->validatePresence('password')
            ->add('password', ['length' => [ 'rule' => ['minLength', 8], 'message' => 'Passwords need to be at least 8 characters long']])
            ->add('password', ['length' => [ 'rule' => ['maxLength', 250], 'message' => 'Passwords need to be at most 250 characters long']]);

        return $validator;
    }

    public function getByIds($userIds)
    {
        return $this->find()
            ->where(["id IN" => $userIds]);
    }

    public function getByFacebookId($facebookId)
    {
        return $this->find()
            ->where(["FacebookUsers.facebook_id" => (int)$facebookId])
            ->contain(['FacebookUsers']);
    }
}

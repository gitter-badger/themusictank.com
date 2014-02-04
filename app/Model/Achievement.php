<?php

App::uses('UserActivity', 'Model');

class Achievement extends AppModel
{	
    public $useDbConfig = 'array';
    
    /**
     * This class is static and does not need to be saved in the database.
     * @var array
     */
    public $records     = array(
        array('id' => 1, 'key' => UserActivity::TYPE_NEW_ACCOUNT,   'name' => 'Welcome to TMT', 'description' => 'Welcome Tanker! No go and rock!'),
        array('id' => 2, 'key' => UserActivity::TYPE_CREATED_ARTIST, 'name' => 'Builder',        'description' => 'By importing your collection, you have added a new artist to our list. Thanks!')
    );
        
    /**
     * Looks up the records by key
     * @param string $key The field being queried
     * @return Achievement Achievement if found or null if not
     */
    public function findByKey($key)
    {
        return $this->_getBy("key", $key);
    }    
        
    /**
     * Looks up the records by id
     * @param integer $id Achievement id
     * @return Achievement Achievement if found or null if not
     */
    public function getById($id)
    {
        return $this->_getBy("id", $id);
    }    
    
    
    /**
     * Looks up the records by a custom key
     * @private
     * @param string $key The field being queried
     * @param mixed $value The value being searched for
     * @return Achievement Achievement if found or null if not
     */
    private function _getBy($key, $value)
    { 
        foreach($this->find('all') as $row)
        {
            if($row["Achievement"][$key] == $value)
            {
                return $row;
            }
        }        
        return null;        
    }
    
}


    // what I wanna do : 
    // when user account anniversary, new + every x
    // when user imports artists to our collection -> builder
    // when user reviews, every x
    // when user reviews Jimmi Hendrix -> gods of guitar
    // when user reviews track challenge of the day, every x
// chaque categorie possible -> Music Whore/Open Minded
//achievment idea: si qqun ecoute tout les tounne possible de un artiste - > groopie   
 //Achievment idea: qqun qui ecoute une meme chanson X nombre de fois -> C'est ma tounne!!   

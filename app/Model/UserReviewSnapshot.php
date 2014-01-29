<?php
App::uses('TableSnapshot', 'Model');
App::uses('CakeSession', 'Model/Datasource');   

class UserReviewSnapshot extends TableSnapshot
{	                  
    public function requiresUpdate($data = null)
    {        
        if(!is_null($data)) $this->data = $data;
        $userId = CakeSession::read('Auth.User.User.id');
        if($userId) return $this->_isExpired($userId, $data[$this->getBelongsToAlias()]["id"]);
        return false;
    }    
    
    public function getByBelongsToId($objId)
    {
         $data = $this->find("first", array(
            "conditions" => array(
                "user_id"   => CakeSession::read('Auth.User.User.id'), 
                strtolower($this->getBelongsToAlias()) . "_id"  => $objId,
                "lastsync > " => time() - (HOUR*12)
            ),
            "fields" => array($this->alias . ".*")
        ));
         
        return $data[$this->alias];
    }      
        
    public function getBelongsToData()
    {
        return array(
            "id"  => $this->data[$this->getBelongsToAlias()]["id"]
        );
    }
    
    public function getExtraSaveFields()
    {
        $userId = CakeSession::read('Auth.User.User.id');
        $objId = $this->data[$this->getBelongsToAlias()]["id"];
        
        $extras = array(
            "lastsync"  => time(),
            "user_id"   => $userId,
            strtolower($this->getBelongsToAlias()) . "_id"  => $objId
        );
                
        $data = $this->_getId($userId, $objId);        
        if($data)
        {            
           $extras["id"] = $data[$this->alias]["id"];
        }
                
        return $extras;
    }
        
    protected function _getId($userId, $objId)
    {
        return $this->find("first", array(
            "conditions" => array(
                "user_id"   => $userId, 
                strtolower($this->getBelongsToAlias()) . "_id"  => $objId
            ),
            "fields" => array("id")
        ));
    }
    
    protected function _isExpired($userId, $objId)
    {
        return !$this->find("count", array(
            "conditions" => array(
                "user_id"   => $userId,
                strtolower($this->getBelongsToAlias()) . "_id"  => $objId,
                "lastsync > " => time() - (HOUR*12)
            )
        )) > 0;
    }    
}
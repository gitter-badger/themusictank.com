<?php
App::uses('TableSnapshot', 'Model');
App::uses('CakeSession', 'Model/Datasource');   

class UserReviewSnapshot extends TableSnapshot
{	                    
    public function requiresUpdate()
    {        
        $userId     = CakeSession::read('Auth.User.User.id');        
        $relationId = $this->getData($this->getBelongsToAlias() . ".id");
        if($userId) return $this->_isExpired($userId, $relationId);
        return false;
    }    
            
    /**
     * Creates or updates a model's snapshot
     * @return boolean True on success, false on failure
     */ 
    public function updateCached()
    {                   
        if($this->requiresUpdate())
        {
            return $this->snap();   
        }   
        
        $relationId = $this->getData($this->getBelongsToAlias() . ".id");
        return $this->_getByBelongsToId($relationId);
    }       
            
    public function getBelongsToData()
    {
        $relationId = $this->getData($this->getBelongsToAlias() . ".id");
        return array(
            "id"  => $relationId
        );
    }
    
    public function getExtraSaveFields()
    {
        $userId = CakeSession::read('Auth.User.User.id');
        $relationId = $this->getData($this->getBelongsToAlias() . ".id");
        $data   = $this->_getId($userId, $relationId);     
        
        $extras = array(
            "lastsync"  => time(),
            "user_id"   => $userId,
            strtolower($this->getBelongsToAlias()) . "_id"  => $relationId
        );
                   
        if($data)
        {            
           $extras["id"] = $data[$this->alias]["id"];
        }
                
        return $extras;
    }
        
    private function _getByBelongsToId($objId)
    {        
         $data = $this->find("first", array(
            "conditions" => array(
                "user_id"   => CakeSession::read('Auth.User.User.id'), 
                strtolower($this->getBelongsToAlias()) . "_id"  => $objId,
                "lastsync > " => time() - (HOUR*12)
            ),
            "fields" => array($this->alias . ".*")
        ));
         
        return count($data) > 0 ? $data[$this->alias] : array();
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
        $data = $this->find("first", array(
            "conditions" => array(
                "user_id"   => $userId,
                strtolower($this->getBelongsToAlias()) . "_id"  => $objId
            )
        ));
        
        if(count($data) > 0)
        {
            return ($data[$this->alias]["lastsync"] + (HOUR*12) < time());
        }
        
        return true;        
    }    
}
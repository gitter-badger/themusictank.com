<?php
/**
 * TableSnapshot is a class that handles common functions for models needing 
 * to save ReviewFrames snapshots.
 */
class TableSnapshot extends AppModel
{	    
    /**
     * Creates or updates a model's snapshot
     * @return boolean True on success, false on failure
     */
    public function snap()
    {   
        return (empty($this->data[$this->alias]["id"])) ? $this->_createSnapshot() : $this->_updateSnapshot();   
    }   
    
    /**
     * Returns the whether or not the cached snapshot is still valid
     * @return boolean True if outdated, false if still ok
     */
    public function requiresUpdate($data = null)
    {        
        if(!is_null($data)) $this->data = $data;
        return $this->data[$this->alias]["lastsync"] + 60*60*12 < time();
    }
    
    /**
     * Returns all the reviewing data based on a Model query. This function does not permit user based queries.
     * @param int $timestamp The starting timestamp for all searches. Defaut 0
     * @return array Matching review frames dataset
     */
    public function getRawCurveData($timestamp = 0)
    {
        $belongsToData  = $this->_getBelongsToData();
        $belongsToLabel = strtolower($this->_getBelongToAlias()) . "_id";
        $belongsToId    = (int)$belongsToData["id"];
        
        return $this->ReviewFrames->getRawCurve(array(
                "ReviewFrames.$belongsToLabel"  => $belongsToId, 
                "ReviewFrames.created >"        => $timestamp
        ));
    }    
    
    /**
     * Returns all the reviewing data based on a Model query. This function permits user based queries.
     * @param array $conditions A series of conditions
     * @return array Matching review frames dataset
     */
    public function getRawCurveSpan($conditions)
    {        
        return $this->ReviewFrames->getRawCurveByCreated($conditions);
    }    
    
    /**
     * Return the most recent reviews frames matching the conditions
     * @param type $conditions A series of conditions
     * @param type $limit Limits the number of results. Default = 5.
     * @return array Matching review frames dataset
     */
    public function getRecentReviews($conditions, $limit = 5)
    {   
        return $this->ReviewFrames->getByCreated($conditions, $limit);
    }    
    
    /**
     * Returns the max, min and average values of all linked model reviews
     * @param integer $belongsToId The id of the object we are pulling data from
     * @param integer $timestamp The earliest timestamp of the span. Defaults to 0.
     * @return boolean True on success, False on failure
     */
    public function getAppreciation($belongsToId, $timestamp = 0)
    {                
        $belongsToAlias = strtolower($this->_getBelongToAlias() . "_id");
        return $this->ReviewFrames->getAppreciation("$belongsToAlias = $belongsToId AND created > $timestamp");
    }        
    
    /**
     * Based on all the data, compiles the general score of the object
     * @param array $curveData A dataset of review frames
     * @return float The average scorein percent
     */
    public function compileScore($curveData)
    {
        $total = 0;
        $count = count($curveData);
        
        if($count > 0)
        {        
            foreach($curveData as $data)     
            {
                $total += $data[0]["calc_groove"];
            }
        
            return $total / count($curveData);
        }
        
        return null;
    }
    
    /**
     * Returns the max, min and average values of all linked model reviews based on a user
     * @param integer $belongsToId The id of the object we are pulling data from
     * @param integer $userId A user id.
     * @param integer $timestamp The earliest timestamp of the span. Defaults to 0.
     * @return boolean True on success, False on failure
     */
    public function getUserAppreciation($belongsToId, $userId, $timestamp = 0)
    {                
        $belongsToAlias = strtolower($this->_getBelongToAlias() . "_id");
        return $this->ReviewFrames->getAppreciation("user_id = $userId AND $belongsToAlias = $belongsToId AND created > $timestamp");
    }        
        
    /** The final number of frames is the resolution's value.
     * Compare to the length in order to sum values that
     * have to be merged to fit the curve's resolution  
     * @param integer $duration
     * @param double $resolution
     * @return integer
     */
    public function resolutionToPositionsPerFrames($duration, $resolution)
    {
        return ($duration > $resolution) ? ($duration  / $resolution) : $duration;
    }
    
    
    public function getSpan($start, $end, $limit = 100)
    {
        return $this->find("all", array(
            "conditions" => array(
                "lastsync >=" => $start,
                "lastsync <=" => $end
            ),
            "order" => array("score_snapshot DESC", "liking DESC", "total DESC"),
            "limit" => $limit,
            "recursive" => false
        ));
    }
    
    
    /**
     * Creates a model's snapshot
     * @private
     * @return boolean True on success, false on failure
     */
    private function _createSnapshot()
    {
        $belongsToData  = $this->_getBelongsToData();         
        $belongsToId    = (int)$belongsToData["id"];
        
        $appreciation   = $this->getAppreciation($belongsToId);
        $curve          = $this->getCurve($belongsToId);
        
        return $this->_validateAndSave($appreciation, $curve);        
    }
    
    /**
     * Updates an existing model snapshot
     * @private
     * @return boolean True on success, false on failure
     */
    private function _updateSnapshot()
    {        
        $belongsToData  = $this->_getBelongsToData();      
        $belongsToId    = (int)$belongsToData["id"];
        $timestamp      = $this->data[$this->alias]["lastsync"];        
       
        $appreciation   = $this->ReviewFrames->mergeAppreciationData($this->data[$this->alias], $this->getAppreciation($belongsToId, $timestamp));
        $curve          = $this->getCurve($belongsToId, 150, $timestamp); 
        
        return $this->_validateAndSave($appreciation, $curve);  
    }        
    
    /**
     * Validates and saves a snapshot
     * @private
     * @param array $appreciation
     * @param array $curve
     * @return boolean True on success, false on failure
     */
    private function _validateAndSave($appreciation, $curve)
    {
        $saveArray = array_merge($appreciation, $this->_getExtraSaveFields());
        
        $saveArray["snapshot_ppf"]      = $curve["ppf"];
        $saveArray["curve_snapshot"]    = $curve["curve"];
        $saveArray["score_snapshot"]    = $curve["score"];
                
        if(isset($saveArray["curve_snapshot"]) && !is_string($saveArray["curve_snapshot"])) 
        {
            $saveArray["curve_snapshot"] = json_encode($saveArray["curve_snapshot"]);
        }
        
        if(array_key_exists("metacritic_score", $curve))
        {
            $saveArray["metacritic_score"] = $curve["metacritic_score"];
        }        
        
        return $this->save($saveArray) ? $saveArray : null;
    }
    
    /**
     * Returns the current values of Model->data for the belongs to Model.
     * @private
     * @return array The preloaded data of the object
     */
    private function _getBelongsToData()
    {
        return $this->data[$this->_getBelongToAlias()];
    }
    
    /**
     * Returns pre-populated extra fields that need to be saved along
     * side each snapshots.
     * @private
     * @return array Data ready to be saved
     */
    private function _getExtraSaveFields()
    {
        $extras = array();
                
        $belongsToAlias = $this->_getBelongToAlias();
        $belongsToData  = $this->_getBelongsToData();        
        
        $extras["lastsync"]  = time();
        $extras[strtolower($belongsToAlias) . "_id"] = (int)$belongsToData["id"];
        
        if((int)$this->data[$this->alias]["id"] > 0)
        {            
           $extras["id"] = $this->data[$this->alias]["id"];
        }
                
        return $extras;
    }
    
    /**
     * Gets the model linked to the snapshot through the belongs to association.
     * Expects that the object only belongs to one parent object.
     * @return string The name of the object.
     */
    private function _getBelongToAlias()
    {
        $belongsAliases = array_keys($this->belongsTo);
        return $belongsAliases[0];
    }
}
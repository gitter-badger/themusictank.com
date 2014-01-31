<?php
/**
 * TableSnapshot is a class that handles common functions for models needing 
 * to save ReviewFrames snapshots.
 */

App::uses('ReviewFrames', 'Model');

class TableSnapshot extends AppModel
{	    
    public function afterFind($results, $primary = false)
    {
        if(!array_key_exists("id", $results))
        {
            foreach($results as $idx => $row)
            {   
                if(array_key_exists($this->alias, $row))
                { 
                    if(array_key_exists("curve_snapshot", $row[$this->alias]) && is_string($row[$this->alias]["curve_snapshot"]))
                    {   
                        $results[$idx][$this->alias]["curve_snapshot"] = json_decode($row[$this->alias]["curve_snapshot"]);
                    }                
                    if(array_key_exists("range_snapshot", $row[$this->alias]) && is_string($row[$this->alias]["range_snapshot"]))
                    {   
                        $results[$idx][$this->alias]["range_snapshot"] = json_decode($row[$this->alias]["range_snapshot"]);
                    }
                }
            }        
        }        
        return $results;        
    }
    
    /**
     * Creates or updates a model's snapshot
     * @return boolean True on success, false on failure
     */ 
    public function updateCached()
    {        
        if($this->requiresUpdate())
        {
            $this->snap();   
        }
    }   
    
    
    /**
     * Creates or updates a model's snapshot
     * @return boolean True on success, false on failure
     */
    public function snap()
    {   
        $id = $this->getData($this->alias . ".id");
        return (empty($id)) ? $this->_createSnapshot() : $this->_updateSnapshot();   
    }   
    
    /**
     * Returns the whether or not the cached snapshot is still valid
     * @return boolean True if outdated, false if still ok
     */
    public function requiresUpdate()
    {                
        $timestamp = $this->getData($this->alias . ".lastsync");
        return empty($timestamp) || $timestamp + (HOUR*12) < time();
    }
    
    /**
     * Returns all the reviewing data based on a Model query. This function does not permit user based queries.
     * @param int $timestamp The starting timestamp for all searches. Defaut 0
     * @return array Matching review frames dataset
     */
    public function getRawCurveData($timestamp = 0, $extraConditions = array())
    {
        $belongsToData  = $this->getBelongsToData();
        $belongsToLabel = strtolower($this->getBelongsToAlias()) . "_id";
        $belongsToId    = (int)$belongsToData["id"];
        
        $conditions = array_merge($extraConditions, array(
            "ReviewFrames.$belongsToLabel"  => $belongsToId, 
            "ReviewFrames.created >"        => $timestamp
        ));
                
        return (new ReviewFrames())->getRawCurve($conditions);
    }    
    
    public function getRawSplitData($threshold, $timestamp = 0, $extraConditions = array())
    {
        $belongsToData  = $this->getBelongsToData();
        $belongsToLabel = strtolower($this->getBelongsToAlias()) . "_id";
        $belongsToId    = (int)$belongsToData["id"];
        $reviews        = new ReviewFrames();
        
        $conditions = array_merge($extraConditions, array(
            "ReviewFrames.$belongsToLabel"  => $belongsToId, 
            "ReviewFrames.created >"        => $timestamp
        ));     
        
        $avgMax = $reviews->getRawCurve(array_merge($conditions, array("ReviewFrames.groove >" => $threshold)));
        $avgMin = $reviews->getRawCurve(array_merge($conditions, array("ReviewFrames.groove <" => $threshold)));
                
        return array("min" => $avgMin, "max" => $avgMax);        
    }    
    
    
    /**
     * Returns all the reviewing data based on a Model query. This function permits user based queries.
     * @param array $conditions A series of conditions
     * @return array Matching review frames dataset
     */
    public function getRawCurveSpan($conditions)
    {        
        return (new ReviewFrames())->getRawCurveByCreated($conditions);
    }    
    
    /**
     * Return the most recent reviews frames matching the conditions
     * @param type $conditions A series of conditions
     * @param type $limit Limits the number of results. Default = 5.
     * @return array Matching review frames dataset
     */
    public function getRecentReviews($conditions, $limit = 5)
    {   
        return (new ReviewFrames())->getByCreated($conditions, $limit);
    }    
    
    /**
     * Returns the max, min and average values of all linked model reviews
     * @param integer $belongsToId The id of the object we are pulling data from
     * @param integer $timestamp The earliest timestamp of the span. Defaults to 0.
     * @return boolean True on success, False on failure
     */
    public function getAppreciation($belongsToId, $timestamp = 0, $extraConditions = null)
    {                
        $belongsToAlias = strtolower($this->getBelongsToAlias() . "_id");
        $conditions = "$belongsToAlias = $belongsToId AND created > $timestamp";
        
        if(!is_null($extraConditions))
        {
            $conditions .= " AND $extraConditions";
        }
        
        return (new ReviewFrames())->getAppreciation($conditions);
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
        $belongsToAlias = strtolower($this->getBelongsToAlias() . "_id");
        return (new ReviewFrames())->getAppreciation("user_id = $userId AND $belongsToAlias = $belongsToId AND created > $timestamp");
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
     * Returns the current values of Model->data for the belongs to Model.
     * @return array The preloaded data of the object
     */
    public function getBelongsToData()
    {
        return $this->data[$this->getBelongsToAlias()];
    }
    
    /**
     * Returns pre-populated extra fields that need to be saved along
     * side each snapshots.
     * @return array Data ready to be saved
     */
    public function getExtraSaveFields()
    {        
        $extras = array();
                
        $belongsToAlias = $this->getBelongsToAlias();
        $belongsToData  = $this->getBelongsToData();        
        
        $extras["lastsync"]  = time();
        $extras[strtolower($belongsToAlias) . "_id"] = (int)$belongsToData["id"];
        $id = (int)$this->getData($this->alias . ".id");
        
        if($id > 0)
        {            
           $extras["id"] = $id;
        }
                
        return $extras;
    }           
    
    /**
     * Creates a model's snapshot
     * @private
     * @return boolean True on success, false on failure
     */
    private function _createSnapshot()
    {        
        $belongsToData  = $this->getBelongsToData();         
        $belongsToId    = (int)$belongsToData["id"];
        
        $appreciation   = $this->getAppreciation($belongsToId);
        $curve          = $this->getCurve($belongsToId, 150);
        
        return $this->_validateAndSave($appreciation, $curve);        
    }
    
    /**
     * Updates an existing model snapshot
     * @private
     * @return boolean True on success, false on failure
     */
    private function _updateSnapshot()
    {        
        $belongsToData  = $this->getBelongsToData();      
        $belongsToId    = (int)$belongsToData["id"];        
        $timestamp      = $this->getData($this->alias . ".lastsync");
       
        $appreciation   = (new ReviewFrames())->mergeAppreciationData($this->data[$this->alias], $this->getAppreciation($belongsToId, $timestamp));
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
        $saveArray = array_merge($appreciation, $this->getExtraSaveFields());
        
        if(array_key_exists("ppf", $curve))
        {
            $saveArray["snapshot_ppf"]      = $curve["ppf"];
        }
        
        if(array_key_exists("curve", $curve))
        {
            $saveArray["curve_snapshot"]    = $curve["curve"];
        }
        
        if(array_key_exists("score", $curve))
        {
            $saveArray["score_snapshot"]    = $curve["score"];
        }
        
        if(array_key_exists("split", $curve))
        {
            $saveArray["range_snapshot"]    = $curve["split"];
        }
                
        if(isset($saveArray["curve_snapshot"]) && !is_string($saveArray["curve_snapshot"])) 
        {
            $saveArray["curve_snapshot"] = json_encode($saveArray["curve_snapshot"]);
        }
        
        if(isset($saveArray["range_snapshot"]) && !is_string($saveArray["range_snapshot"])) 
        {
            $saveArray["range_snapshot"] = json_encode($saveArray["range_snapshot"]);
        }
                
        return $this->save($saveArray) ? $saveArray : null;
    }
        
    /**
     * Gets the model linked to the snapshot through the belongs to association.
     * Expects that the object only belongs to one parent object.
     * @return string The name of the object.
     */
    public function getBelongsToAlias()
    {
        $belongsAliases = array_keys($this->belongsTo);
        return $belongsAliases[0];
    }
}
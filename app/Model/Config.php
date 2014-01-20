<?php

class Config extends AppModel
{	
    const TYPE_DAILY    = "daily";
    const TYPE_WEEKLY   = "weekly";
    const TYPE_MONTHLY  = "monthly";
    const TYPE_YEARLY   = "yearly";
    
    
    /**
     * Preloads all the configuration options related to a daily update
     * @return array Dataset
     */
    public function getDaily()
    {
        return $this->find("all", array(
            "conditions" => array("Config.key" => array(
                "last_heavyrotation_sync", 
                "last_trackchallenge_sync",
               // "last_dailychart_sync"
            ))
        ));
    }
    
    /**
     * Preloads all the configuration options related to a weekly update
     * @return array Dataset
     */
    public function getWeekly()
    {
        return $this->find("all", array(
            "conditions" => array("Config.key" => array(
                "last_newreleases_sync",
                "last_weeklychart_sync"
            ))
        ));
    }
    
        /**
     * Preloads all the configuration options related to a yearly update
     * @return array Dataset
     */
    public function getYearly()
    {
        return $this->find("all", array(
            "conditions" => array("Config.key" => array(
             //   "last_yearlychart_sync"
            ))
        ));
    }
 
    /**
     * Updates the last heavy rotation sync time to now
     * @return boolean True on success, False on failure.
     */
    public function setNewReleasesUpdate()
    {
        return $this->_setSyncUpdate("last_newreleases_sync");
    }  
 
    /**
     * Updates the last heavy rotation sync time to now
     * @return boolean True on success, False on failure.
     */
    public function setPopularArtistUpdate()
    {
        return $this->_setSyncUpdate("last_heavyrotation_sync");
    }    
    
    /**
     * Updates the track challenge sync time to now
     * @return boolean True on success, False on failure.
     */
    public function setTrackChallengeUpdate()
    {        
        return $this->_setSyncUpdate("last_trackchallenge_sync");
    }
    /*
    public function setDailyTrackChartUpdate()
    {        
        return $this->_setSyncUpdate("last_dailychart_sync");
    }*/
    
    public function setWeeklyTrackChartUpdate()
    {        
        return $this->_setSyncUpdate("last_weeklychart_sync");
    }
    /*
    public function setMonthlyTrackChartUpdate()
    {        
        return $this->_setSyncUpdate("last_monthlychart_sync");
    }
    
    public function setYearlyTrackChartUpdate()
    {        
        return $this->_setSyncUpdate("last_yearlychart_sync");
    }*/
    
    /**
     * Specifies if the popular artists need to be updated.
     * @param type $data
     * @return boolean True is content is not up to date, False if it is
     */
    public function requiresPopularArtistUpdate($data = null)
    {        
        if(!is_null($data)) $this->data = $data; 
        return $this->_validateDelay("last_heavyrotation_sync", 60*60*24);
    }
    
    /**
     * Specifies if the popular artists need to be updated.
     * @param type $data
     * @return boolean True is content is not up to date, False if it is
     */
    public function requiresNewReleasesUpdate($data = null)
    {  
        if(!is_null($data)) $this->data = $data;
        return $this->_validateDelay("last_newreleases_sync", 60*60*24*7);  
    }
    
    /**
     * Specifies if the track challenge needs to be updated.
     * @param type $data
     * @return boolean True is content is not up to date, False if it is
     */
    public function requiresDailyTrackChallengeUpdate($data = null)
    {
        if(!is_null($data)) $this->data = $data;  
        return $this->_validateDelay("last_trackchallenge_sync", 60*60*24);  
    }
    /*
    public function requiresDailyChartsUpdate($data = null)
    {     
        if(!is_null($data)) $this->data = $data; 
        return $this->_validateDelay("last_dailychart_sync", 60*60*24);
    }*/
    
    public function requiresWeeklyChartsUpdate($data = null)
    {
        if(!is_null($data)) $this->data = $data;
        return $this->_validateDelay("last_weeklychart_sync", 60*60*24*7);
    }
    /*
    public function requiresMonthlyChartsUpdate($data = null)
    {
        if(!is_null($data)) $this->data = $data;
        return $this->_validateDelay("last_monthlychart_sync", 60*60*24*30);
    }
    
    public function requiresYearlyChartsUpdate($data = null)
    {
        if(!is_null($data)) $this->data = $data;
        return $this->_validateDelay("last_yearlychart_sync", 60*60*24*365);
    }*/
    
    /**
     * Based on preloaded values, fetches a unique record. This internally
     * allows only one query to update the config
     * @param string $key The unique config key 
     * @return array Correct row on success, null on failure
     */
    private function _getRecordByKey($key)
    {
        foreach($this->data as $config)
        {
            if($config["Config"]["key"] == $key)
            {
                return $config;
            }
        }
        return null;
    }
    
    private function _setSyncUpdate($key)
    {
        $record = $this->_getRecordByKey($key);    
        return $this->save(array(
            "id"        => is_null($record) ? $record["id"] : null,
            "key"       => $key,
            "value"     => time()
        ));
    }
    
    private function _validateDelay($key, $syncDelay)
    {
        $record = $this->_getRecordByKey($key);        
        if($record)
        {            
            return (int)$record["Config"]["value"] + $syncDelay < time();
        }
        
        return true;
    }
    
}
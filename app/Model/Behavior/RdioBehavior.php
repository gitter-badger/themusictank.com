<?php

$vendor = App::path('Vendor');        
require_once ($vendor[0] . "rdio-simple/rdio.php"); 

class RdioBehavior extends ModelBehavior {       
    
    
    public function _getRdioInstance()
    {
        return new Rdio(Configure::read('RdioApiConfig'));
    }
    
    public function getRdioArtistLibrary($model, $key)
    {
        $data = $this->_getRdioInstance()->call('getArtistsInCollection', array("user" => $key));        
        return ($data) ? $data->result : null;
    }
    
    public function getTracksFromRdio($model, $key)
    {
        $data = $this->_getRdioInstance()->call('get', array("keys" => $key, "extras" => "tracks"));           
        return ($data) ? $data->result->{$key}->tracks : null;        
    }
        
    public function getRdioAlbumsForArtists($model, $rdioKey)
    {
        $data = $this->_getRdioInstance()->call('getAlbumsForArtist', array("artist" => $rdioKey));
        return ($data) ? $data->result : null;
    }
    
    public function getRdioHeavyRotation($model, $type = "artists")
    {
        $data = $this->_getRdioInstance()->call('getHeavyRotation', array("type" => $type));   
        return ($data) ? $data->result : null;
    }
     
    public function getRdioNewReleases($model, $time = "thisweek")
    {
        $data = $this->_getRdioInstance()->call('getNewReleases', array("time" => $time, "extras" => "tracks"));  
        return ($data) ? $data->result : null;
    }   
    
    public function getPlaybackToken()
    {
        $data = $this->_getRdioInstance->call('getPlaybackToken', array("domain" => $_SERVER['SERVER_NAME']));  
        return ($data) ? $data->result : null;
    }
                
}
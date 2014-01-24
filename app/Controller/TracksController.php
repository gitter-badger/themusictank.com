<?php
class TracksController extends AppController {
                    
	var $components = array("EchonestApi", "LastfmApi");
    var $helpers    = array("Chart");
    
    /** 
     * Track profile page.
     *
     * @param string $artistSlug Artist slug
     * @param string $albumSlug Artist slug
     * @param string $trackSlug Album slug
     */
    public function view($trackSlug)
    {
        $this->usesPlayer();
        
        $data = $this->Track->getBySlugContained($trackSlug);
        $needsRefresh = false;
                
        if($this->Track->LastfmTrack->requiresUpdate($data))
        {   
            $needsRefresh = $this->requestAction(array("controller" => "tracks", "action" => "syncTrackDetails"));
        }
        
        if($needsRefresh)
        {
            $data = $this->Track->getBySlugContained($trackSlug);
        }
        
        if($this->Track->TrackReviewSnapshot->requiresUpdate($data))
        {            
            $data["TrackReviewSnapshot"] = $this->Track->TrackReviewSnapshot->snap();
        }
        
        $this->set("track", $data["Track"]);    
        $this->set("rdioTrack", $data["RdioTrack"]);    
        $this->set("lastfmTrack", $data["LastfmTrack"]);    
        $this->set("album", $data["Album"]);     
        $this->set("artist", $data["Album"]["Artist"]);       
        $this->set("snapshot", $data["TrackReviewSnapshot"]);        
                
        $this->setPageTitle(array($data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]));
        $this->setPageMeta(array(
            "keywords" => array($data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]),
            "description" => __("Listening statistics of ") . $data["Track"]["title"] . __(", a track featured on ") . $data["Album"]["name"] . __(", an album by ") . $data["Album"]["Artist"]["name"] . _(' released ') . date("F j Y", $data["Album"]["release_date"]) . "."
        ));
    } 
    
    
    /** 
     * Inline call that syncs the LastFm information of the preloaded Track. 
     *
     * @return boolean True on success, false on failure
     */
    public function syncTrackDetails()
    {           
        $data           = $this->Track->LastfmTrack->data;
        $trackTitle     = $data["Track"]["title"];
        $artistName     = $data["Album"]["Artist"]["name"];
        $infos          = $this->LastfmApi->getAlbumDetails($trackTitle, $artistName);
                
        if($infos)
        {
            return $this->Track->LastfmTrack->saveDetails($data, $infos) !== false;
        }        
        return false;
    }        
}

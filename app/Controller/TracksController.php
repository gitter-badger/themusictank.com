<?php
class TracksController extends AppController {
                    
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
        
        $isLoggedIn = $this->userIsLoggedIn();
        $data       = $this->Track->getUpdatedSetBySlug($trackSlug, $isLoggedIn);
        
        $chartConfig = array(
            "track" => $data["Track"], 
            "rdioTrack" => $data["RdioTrack"]["id"], 
            "player" => $this->preferredPlayer, 
            "trackReviewSnapshot" => $data["TrackReviewSnapshot"]
        );
                
        $this->set("track", $data["Track"]);    
        $this->set("rdioTrack", $data["RdioTrack"]);    
        $this->set("lastfmTrack", $data["LastfmTrack"]);    
        $this->set("album", $data["Album"]);     
        $this->set("artist", $data["Album"]["Artist"]);  
        $this->set("trackReviewSnapshot", $data["TrackReviewSnapshot"]);        
        
        if($isLoggedIn) {
            $this->set("userTrackReviewSnapshot", $data["UserTrackReviewSnapshot"]); 
            $chartConfig["userTrackReviewSnapshot"] = $data["UserTrackReviewSnapshot"];
            
            $this->set("subsTrackReviewSnapshot", $data["SubscribersTrackReviewSnapshot"]); 
            $chartConfig["subsTrackReviewSnapshot"] = $data["SubscribersTrackReviewSnapshot"];
        }
                
        $this->set("trackChartConfig", $chartConfig);
                
        $this->setPageTitle(array($data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]));
        $this->setPageMeta(array(
            "keywords" => array($data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]),
            "description" => __("Listening statistics of ") . $data["Track"]["title"] . 
                            __(", a track featured on ") . $data["Album"]["name"] . 
                            __(", an album by ") . $data["Album"]["Artist"]["name"] . 
                            __(' released ') . date("F j Y", $data["Album"]["release_date"]) . "."
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

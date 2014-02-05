<?php
class TracksController extends AppController {
                    
    var $helpers    = array("Chart", "Time");
    
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
                        
        $this->set("track", $data["Track"]);    
        $this->set("rdioTrack", $data["RdioTrack"]);    
        $this->set("lastfmTrack", $data["LastfmTrack"]);    
        $this->set("album", $data["Album"]);     
        $this->set("artist", $data["Album"]["Artist"]);  
        $this->set("trackReviewSnapshot", $data["TrackReviewSnapshot"]);        
        
        if($isLoggedIn) {
            $this->set("userTrackReviewSnapshot", $data["UserTrackReviewSnapshot"]);             
            $this->set("subsTrackReviewSnapshot", $data["SubscribersTrackReviewSnapshot"]);
        }
                
        $this->set("oembedLink", $this->Track->getOEmbedUrl());
                
        $this->setPageTitle(array($data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]));
        $this->setPageMeta(array(
            "keywords" => array($data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]),
            "description" => sprintf(
                __("Listening statistics of %s, a track featured on %s. An album by %s that was released on %s."), 
                $data["Track"]["title"],
                $data["Album"]["name"],
                $data["Album"]["Artist"]["name"],
                date("F j Y", $data["Album"]["release_date"])
             )
        ));
    } 
        
    /** 
     * Album profile page.
     * @param string $artistSlug Artist slug (unused)
     * @param string $trackSlug Album slug
     */
    public function embed($trackSlug)
    {   
        $this->usesPlayer();
        $this->layout = "blank";
        
        $data = $this->Track->getUpdatedSetBySlug($trackSlug);

        $this->set("track", $data["Track"]);    
        $this->set("rdioTrack", $data["RdioTrack"]);    
        $this->set("lastfmTrack", $data["LastfmTrack"]);    
        $this->set("album", $data["Album"]);     
        $this->set("artist", $data["Album"]["Artist"]);  
        $this->set("trackReviewSnapshot", $data["TrackReviewSnapshot"]); 
        $this->set("oembedLink", $this->Track->getOEmbedUrl());
    }
}

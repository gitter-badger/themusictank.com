<?php
class TracksController extends AppController {
                    
    var $helpers    = array("Chart", "Time");
    
    public function beforeFilter()
    {   
        parent::beforeFilter();   
        $this->Auth->deny(array("by_subscriptions"));
    }    
    
      
   
    
    
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
        if(!$data) throw new NotFoundException(sprintf(__("Could not find the track %s"), $trackSlug));
                
        $this->set("track", $data["Track"]);    
        $this->set("rdioTrack", $data["RdioTrack"]);    
        $this->set("lastfmTrack", $data["LastfmTrack"]);    
        $this->set("album", $data["Album"]);     
        $this->set("artist", $data["Album"]["Artist"]);  
        $this->set("trackReviewSnapshot", $data["TrackReviewSnapshot"]);        
        
        $this->set("usersWhoReviewed", $this->User->getReviewUserSummary($data["Track"]["id"]));
        
        if($isLoggedIn) {
            $this->set("userTrackReviewSnapshot", $data["UserTrackReviewSnapshot"]);             
            $this->set("subsTrackReviewSnapshot", $data["SubscribersTrackReviewSnapshot"]);
            $this->set("subsWhoReviewed", $this->User->getCommonSubscriberReview($this->getAuthUserId(), $data["Track"]["id"]));
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
        if(!$data) throw new NotFoundException(sprintf(__("Could not find the track %s"), $trackSlug));

        $this->set("track", $data["Track"]);    
        $this->set("rdioTrack", $data["RdioTrack"]);    
        $this->set("lastfmTrack", $data["LastfmTrack"]);    
        $this->set("album", $data["Album"]);     
        $this->set("artist", $data["Album"]["Artist"]);  
        $this->set("trackReviewSnapshot", $data["TrackReviewSnapshot"]); 
        $this->set("oembedLink", $this->Track->getOEmbedUrl());
    }
    
    public function by_user($trackSlug, $userSlug)
    {
        $this->usesPlayer();        
             
        $isLoggedIn = $this->userIsLoggedIn();
        
        $data = $this->Track->getUpdatedSetBySlug($trackSlug, $isLoggedIn);        
        if(!$data) throw new NotFoundException(sprintf(__("Could not find the track %s"), $trackSlug));
                
        $this->set("track", $data["Track"]);    
        $this->set("rdioTrack", $data["RdioTrack"]);    
        $this->set("lastfmTrack", $data["LastfmTrack"]);    
        $this->set("album", $data["Album"]);     
        $this->set("artist", $data["Album"]["Artist"]);  
        $this->set("trackReviewSnapshot", $data["TrackReviewSnapshot"]); 
        
        if($isLoggedIn) {
            $this->set("userTrackReviewSnapshot", $data["UserTrackReviewSnapshot"]);             
        }
                
        if($userSlug != $this->Session->read('Auth.User.User.slug'))
        {        
            $userData = $this->User->findBySlug($userSlug, array("fields" => "User.*"));
            if(!$userData) throw new NotFoundException(sprintf(__("Could not find the user %s"), $userSlug));

            $this->set("viewingUser", $userData["User"]);

            $this->User->data = $data;
            $this->set("viewingTrackReviewSnapshot", $this->User->getUncachedSnapshot($userData["User"]["id"]));
        }
        else {
            $this->set("viewingUser", $this->Session->read('Auth.User.User'));            
        }
                
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
    
    public function by_subscriptions($trackSlug)
    {        
        $data = $this->Track->getBySlugContained($trackSlug);
        if(!$data) throw new NotFoundException(sprintf(__("Could not find the track %s"), $trackSlug));
        
        $this->set("track", $data["Track"]);
        $this->set("album", $data["Album"]);     
        $this->set("artist", $data["Album"]["Artist"]);        
        $this->set("usersWhoReviewed", $this->User->getCommonSubscriberReview($this->getAuthUserId(), $data["Track"]["id"]));        
                        
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
    
}

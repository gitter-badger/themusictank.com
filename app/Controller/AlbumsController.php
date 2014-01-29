<?php
/**
 * AlbumsController controller
 *
 * Contains Album pages methods
 *
 * @package       app.Controller
 */
class AlbumsController extends AppController {
    
    public $helpers    = array("Chart");
	public $components = array("RdioApi", "MetacriticApi", "LastfmApi");     
                
    /** 
     * Album profile page.
     * @param string $artistSlug Artist slug (unused)
     * @param string $albumSlug Album slug
     */
    public function view($albumSlug)
    {   
        $this->usesPlayer();
        
        $data = $this->Album->findBySlug($albumSlug);
        $needsRefresh = false;
        
        $this->Album->RdioAlbum->data = $data;
        if($this->Album->RdioAlbum->requiresUpdate())
        {
            $needsRefresh = $this->requestAction(array("controller" => "albums", "action" => "syncAlbumTracks"));
        }
        
        $this->Album->LastfmAlbum->data = $data;
        if($this->Album->LastfmAlbum->requiresUpdate())
        {
            $needsRefresh = $this->requestAction(array("controller" => "albums", "action" => "syncAlbumDetails")) && $needsRefresh;
        }
        
        if($needsRefresh)
        {
            $data = $this->Album->findBySlug($albumSlug);
        }        
        
        $this->Album->AlbumReviewSnapshot->data = $data;
        $data["AlbumReviewSnapshot"] = $this->Album->AlbumReviewSnapshot->getSnapshot();                
        
        if($this->userIsLoggedIn())
        {   
            $this->User->UserAlbumReviewSnapshot->data = $data;
            $data["UserAlbumReviewSnapshot"] = $this->User->UserAlbumReviewSnapshot->getSnapshot();
            $this->set("userAlbumReviewSnapshot", $data["UserAlbumReviewSnapshot"]);  
        }
                
        $this->set("album",     $data["Album"]);
        $this->set("rdioAlbum", $data["RdioAlbum"]);  
        $this->set("lastfmAlbum", $data["LastfmAlbum"]);    
        $this->set("tracks",    $data["Tracks"]);
        $this->set("artist",    $data["Artist"]);
        $this->set("albumReviewSnapshot",  $data["AlbumReviewSnapshot"]);                
        
        $this->setPageTitle(array($data["Album"]["name"], $data["Artist"]["name"]));
        $this->setPageMeta(array(
            "keywords" => array($data["Album"]["name"], $data["Artist"]["name"]),
            "description" => __("Listening statistics of ") . $data["Album"]["name"] . __(", an album by ") . $data["Artist"]["name"] . _(' released ') . $data["Album"]["release_date"] . "."
        ));
    }            
               
    /** 
     * New albums page.
     */
    public function newReleases()
    {   
        $weekDate = date("F j Y", mktime(0, 0, 0, date("n"), date("j") - date("N")));
        $title = __("New releases for the week of") . " " . $weekDate . ".";
        
        $this->set("newReleases", $this->Album->getNewReleases());
        $this->set("forTheWeekOf", $weekDate);
        
        $this->setPageTitle(array($title));
        $this->setPageMeta(array(
            "keywords" => array(__("New Releases")),
            "description" => $title
        ));
    }      
    
    
    /** 
     * Inline call that fetched track information for the preloaded Album.
     * @return boolean True on success, false on failure
     */
    public function syncAlbumTracks()
    {   
        $data           = $this->Album->RdioAlbum->data;
        $rdioAlbumKey   = $data["RdioAlbum"]["key"];       
        $albumId        = $data["Album"]["id"];
        $tracks         = $this->RdioApi->getTracksForAlbum($rdioAlbumKey);        
        
        if($tracks)
        {
            $this->loadModel("Track");
            $this->Track->filterNewAndSave($tracks, $albumId);            
            return $this->Album->RdioAlbum->setSyncTimestamp($data);
        }        
        return false;        
    }
    
    
    /** 
     * Inline call that syncs the LastFm information of the preloaded Album. 
     *
     * @return boolean True on success, false on failure
     */
    public function syncAlbumDetails()
    {           
        $data           = $this->Album->LastfmAlbum->data;
        $albumName      = $data["Album"]["name"];
        $artistName     = $data["Artist"]["name"];
        $infos          = $this->LastfmApi->getAlbumDetails($albumName, $artistName);
                
        if($infos)
        {
            return $this->Album->LastfmAlbum->saveDetails($data, $infos) !== false;
        }        
        return false;
    }        
    
}
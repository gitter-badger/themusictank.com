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
                
    /** 
     * Album profile page.
     * @param string $artistSlug Artist slug (unused)
     * @param string $albumSlug Album slug
     */
    public function view($albumSlug)
    {   
        $this->usesPlayer();
        
        $isLoggedIn = $this->userIsLoggedIn();
        $data       = $this->Album->getUpdatedSetBySlug($albumSlug, $isLoggedIn);
                        
        $this->set("album",     $data["Album"]);
        $this->set("rdioAlbum", $data["RdioAlbum"]);  
        $this->set("lastfmAlbum", $data["LastfmAlbum"]);    
        $this->set("tracks",    $data["Tracks"]);
        $this->set("artist",    $data["Artist"]);
        $this->set("albumReviewSnapshot",  $data["AlbumReviewSnapshot"]);     
        
        if($isLoggedIn) {
            $this->set("userAlbumReviewSnapshot", $data["UserAlbumReviewSnapshot"]); 
            $this->set("subsAlbumReviewSnapshot", $data["SubscribersAlbumReviewSnapshot"]); 
        }
                
        $this->set("oembedLink", $this->Album->getOEmbedUrl());
        $this->setPageTitle(array($data["Album"]["name"], $data["Artist"]["name"]));
        $this->setPageMeta(array(
            "keywords" => array($data["Album"]["name"], $data["Artist"]["name"]),
            "description" => __("Listening statistics of ") . $data["Album"]["name"] . 
                            __(", an album by ") . $data["Artist"]["name"] . _(' released ') . 
                            date("F j Y", $data["Album"]["release_date"]) . "."
        ));
    }            
    
    /** 
     * Album profile page.
     * @param string $artistSlug Artist slug (unused)
     * @param string $albumSlug Album slug
     */
    public function embed($albumSlug)
    {   
        $this->usesPlayer();
        $this->layout = "blank";
        
        $data = $this->Album->getUpdatedSetBySlug($albumSlug);
                        
        $this->set("album",     $data["Album"]);
        $this->set("rdioAlbum", $data["RdioAlbum"]);  
        $this->set("lastfmAlbum", $data["LastfmAlbum"]);    
        $this->set("tracks",    $data["Tracks"]);
        $this->set("artist",    $data["Artist"]);
        $this->set("albumReviewSnapshot",  $data["AlbumReviewSnapshot"]);    
                
        $this->set("oembedLink", $this->Album->getOEmbedUrl());
        $this->setPageTitle(array($data["Album"]["name"], $data["Artist"]["name"]));
        $this->setPageMeta(array(
            "keywords" => array($data["Album"]["name"], $data["Artist"]["name"]),
            "description" => __("Listening statistics of ") . $data["Album"]["name"] . 
                            __(", an album by ") . $data["Artist"]["name"] . _(' released ') . 
                            date("F j Y", $data["Album"]["release_date"]) . "."
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
}
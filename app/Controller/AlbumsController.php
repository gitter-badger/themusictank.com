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
        // Prepare the view variables 
        $this->usesPlayer();
        
        $isLoggedIn = $this->userIsLoggedIn();
        $data       = $this->Album->getUpdatedSetBySlug($albumSlug, $isLoggedIn);

        if(!$data)
        {
            throw new NotFoundException(sprintf(__("Could not find the album %s"), $albumSlug));
        }
        
        // Build general objects
        $this->set("album",         $data["Album"]);
        $this->set("rdioAlbum",     $data["RdioAlbum"]);  
        $this->set("lastfmAlbum",   $data["LastfmAlbum"]);    
        $this->set("artist",        $data["Artist"]);
        $this->set("oembedLink",    $this->Album->getOEmbedUrl());
            
        // Associate review snapshots.
        $this->set("albumReviewSnapshot",  $data["AlbumReviewSnapshot"]);     

        if($isLoggedIn)
        {
            $this->set("userAlbumReviewSnapshot", $data["UserAlbumReviewSnapshot"]); 
            $this->set("subsAlbumReviewSnapshot", $data["SubscribersAlbumReviewSnapshot"]); 
        }

        $this->Album->addTracksSnapshots();
        $this->set("tracks", $this->Album->data["Tracks"]);
                        
        // Set meta information
        $this->setPageTitle(array($data["Album"]["name"], $data["Artist"]["name"]));
        $this->setPageMeta(array(
            "keywords" => array($data["Album"]["name"], $data["Artist"]["name"], __("Review"), __("Statistics")),
            "description" => sprintf(
                __("View the reviewing statistics of %s, an album by %s that was released %s."),
                $data["Album"]["name"],
                $data["Artist"]["name"],
                date("F j Y", $data["Album"]["release_date"])
            )
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
        
        if(!$data)
        {
            throw new NotFoundException(sprintf(__("Could not find the album %s"), $albumSlug));
        }
                        
        $this->set("album",         $data["Album"]);
        $this->set("rdioAlbum",     $data["RdioAlbum"]);  
        $this->set("lastfmAlbum",   $data["LastfmAlbum"]);    
        $this->set("tracks",        $data["Tracks"]);
        $this->set("artist",        $data["Artist"]);
        $this->set("oembedLink",    $this->Album->getOEmbedUrl());
        $this->set("albumReviewSnapshot",  $data["AlbumReviewSnapshot"]);    
                
        $this->setPageTitle(array($data["Album"]["name"], $data["Artist"]["name"]));
        $this->setPageMeta(array(
            "keywords" => array($data["Album"]["name"], $data["Artist"]["name"], __("Review"), __("Statistics"), __("Embed")),
            "description" => sprintf(
                __("View the reviewing statistics of %s, an album by %s that was released %s."),
                $data["Album"]["name"],
                $data["Artist"]["name"],
                date("F j Y", $data["Album"]["release_date"])
            )
        ));
    }    
               
    /** 
     * New albums page.
     */
    public function newReleases()
    {   
        $weekDate = date("F j Y", mktime(0, 0, 0, date("n"), date("j") - date("N")));
        $title = sprintf(__("New releases for the week of %s"), $weekDate);
        
        $this->set("newReleases", $this->Album->getNewReleases());        
        $this->set("forTheWeekOf", $weekDate);
        
        $this->setPageTitle(array($title));
        $this->setPageMeta(array(
            "keywords" => array(__("New releases"), __("Albums")),
            "description" => sprintf(__("Browse the list of the albums recently added on The Music Tank."))
        ));
    }
}
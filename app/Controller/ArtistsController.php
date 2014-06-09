<?php
/**
 * ArtistsController controller
 *
 * Contains Artists pages methods
 *
 * @package       app.Controller
 */
class ArtistsController extends AppController {
    
    var $helpers    = array("Chart");
	var $components = array("Paginator");
    var $paginate = array(
        'limit' => 25,
        'order' => array(
            'Artist.name' => 'asc'
        )
    );    
                    
    /** 
     * Artist list landing page. Displays CTAs and insentives
     */
    public function index()
    {                
        $this->set("popularArtists",    $this->Artist->findPopular(8));
        $this->set("artistCategories",  $this->Artist->getCategories());        
        $this->set("newReleases",       $this->Artist->Albums->getNewReleases(8));
                        
        $this->setPageTitle(__("Artist list"));        
        $this->setPageMeta(array(
            "keywords" => __("Artist list"),
            "description" => __("Listing of all artists covered in The Music Tank's reviewing pool.")
        ));
    }
                    
    /** 
     * Artist profile page.
     *
     * @param string $artistSlug Artist slug
     */
    public function view($artistSlug)
    {   
        $isLoggedIn = $this->userIsLoggedIn();
        $data       = $this->Artist->getUpdatedSetBySlug($artistSlug, $isLoggedIn);
                
        if(!$data)
        {
            throw new NotFoundException(sprintf(__("Could not find the artist %s"), $artistSlug));
        }
        
        $this->set("artist",        $data["Artist"]);
        $this->set("lastfmArtist",  $data["LastfmArtist"]);        
        $this->set("albums",        $data["Albums"]);
        

        $artistSnapshot = Hash::extract($this->Artist->getSnapshot(), "artistReviewSnapshot");
        $this->set("artistReviewSnapshot", $artistSnapshot);
        
        if($isLoggedIn)
        {
            //$this->set("userArtistReviewSnapshot",  Hash::extract($this->Artist->getUserSnapshot($this->getAuthUserId()), "UserArtistReviewSnapshot"));
            //$this->set("subsArtistReviewSnapshot", Hash::extract($this->Artist->getUserSubscribersSnapshot($this->getAuthUserId()), "SubscribersArtistReviewSnapshot"));
            //$this->set("subsWhoReviewed", $this->User->getCommonSubscriberAlbumReview($this->getAuthUserId(), $data["Album"]["id"]));
        }

        $this->setPageTitle(array($data["Artist"]["name"]));        
        $this->setPageMeta(array(
            "keywords" => array($data["Artist"]["name"]),      
            "description" => __("Listening statistics of ") . $data["Artist"]["name"] . __("'s discography.")
        ));
    }
    
    /** 
     * Browse artists by letter.
     *
     * @param string $letter A supported letter
     */
    public function browse($letter)
    {
        $this->set('artists', $this->Paginator->paginate('Artist', array('Artist.name LIKE' => trim($letter)."%")));
        $this->set("artistCategories",  $this->Artist->getCategories());
        
        $title = __("Browsing") . ": \"". trim($letter) ."\"";
        
        $this->set("title", $title);
        $this->setPageTitle(array($title, __("Artist list")));                
        $this->setPageMeta(array(
            "description" => __("Browing The Music Tank's list of artists by letter ") . trim($letter)
        ));
    }    
    
    /** 
     * Browse artists by term. Renders same view as browse action.
     */
    public function search()
    {
        if($this->request->is('get'))
        {
            $this->set('artists', $this->Paginator->paginate('Artist', array('Artist.name LIKE' => "%". trim($this->request->query['name'])."%")));
            $title = sprintf(__("Searching for: \"%s\""), trim($this->request->query['name']));
        }
        else
        {   
            $title = __("Search");                 
        }        
        
        $this->set("artistCategories",  $this->Artist->getCategories());
        $this->set("title", $title);
        $this->setPageTitle(array($title, __("Artist list")));
        $this->setPageMeta(array(
            "description" => __("Search page")
        ));
        
        $this->render("browse");
    }    
        
}
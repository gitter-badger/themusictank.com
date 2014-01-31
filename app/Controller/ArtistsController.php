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
	var $components = array("RdioApi", "Paginator");
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
        $this->loadModel("Album");
        $this->set("popularArtists",    $this->Artist->findAllPopular());
        $this->set("artistCategories",  $this->Artist->getCategories());        
        $this->set("newReleases",       $this->Album->getNewReleases(5));
                        
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
        
        $this->set("artist",        $data["Artist"]);
        $this->set("rdioArtist",    $data["RdioArtist"]);
        $this->set("lastfmArtist",  $data["LastfmArtist"]);
        $this->set("albums",        $data["Albums"]);
        $this->set("artistReviewSnapshot",      $data["ArtistReviewSnapshot"]);
                        
        $this->setPageTitle(array($data["Artist"]["name"]));        
        $this->setPageMeta(array(
            "keywords" => array($data["Artist"]["name"]),      
            "description" => __("Listening statistics of ") . $data["Artist"]["name"] . _("'s discography.")
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
            $this->set('artists', $this->Paginator->paginate('Artist', array('Artist.name LIKE' => "%". trim($_GET['name'])."%")));
            $title = __("Searching for") . ": \"". trim($_GET['name']) ."\"";
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
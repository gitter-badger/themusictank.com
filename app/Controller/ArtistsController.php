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
	var $components = array("RdioApi", "Paginator", /*"EchonestApi",*/ "LastfmApi");
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
        $this->set("popularArtists",    $this->Artist->findAllPopular());
        $this->set("artistCategories",  $this->Artist->getCategories());
        
        $this->loadModel("Album");
        $this->set("newReleases", $this->Album->getNewReleases(5));
                        
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
        $data = $this->Artist->findBySlug($artistSlug);
        $needsRefresh = false;
        
        if($this->Artist->RdioArtist->requiresUpdate($data))
        {
            $needsRefresh = $this->requestAction(array("controller" => "artists", "action" => "syncArtistDiscography"));
        }
            
        // There is a bug when sunc artists details creates a row.
        // it doesn' add the new lasfmartist.id in the array and
        // primary key fails. something like that.
        if($this->Artist->LastfmArtist->requiresUpdate($data))
        {
            $details            = $this->requestAction(array("controller" => "artists", "action" => "syncArtistDetails"));
            // Quick reasign the data on the model since the previous request cleared it.
            $this->Artist->LastfmArtist->data = $data;
            $albumPopularity    = $this->requestAction(array("controller" => "artists", "action" => "syncArtistNotableAlbums"));
            $needsRefresh       = $details && $albumPopularity && $needsRefresh;
        }
                
        if($needsRefresh)
        {
            $data = $this->Artist->findBySlug($artistSlug);
        }     
        
        if($this->Artist->ArtistReviewSnapshot->requiresUpdate($data))
        {
            $data["ArtistReviewSnapshot"] = $this->Artist->ArtistReviewSnapshot->snap();
        }
        
        if($this->userIsLoggedIn())
        {             
            $data["UserArtistReviewSnapshot"] = $this->User->UserArtistReviewSnapshot->requiresUpdate($data) ?
                $this->User->UserArtistReviewSnapshot->snap() :
                $this->User->UserArtistReviewSnapshot->getByArtistId($data["Artist"]["id"]);
                        
            $this->set("userArtistReviewSnapshot", $data["UserArtistReviewSnapshot"]);  
        }
        

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
    
    /** 
     * Loads the artist library of the current user and compares the list with ours.
     * If new matches are found, they are then saved in the DB.
     */
    public function syncUserLibrary()
    {   
        $rdioUserData = $this->User->RdioUser->getFromUserId($this->getAuthUserId());
        if($rdioUserData)
        {
            if($this->User->RdioUser->requiresUpdate($rdioUserData))
            {         
                $artists = $this->RdioApi->getArtistLibrary();
                if($artists)
                {
                    $filtered = $this->Artist->RdioArtist->filterNew($artists);
                    $this->Artist->saveMany($filtered, array('deep' => true));                
                    $this->User->RdioUser->setSyncTimestamp($rdioUserData);
                }
            }
        }

        $user = $this->Session->read('Auth.User.User'); 
        if($user)
        {
            $this->redirectByRURL(array("controller" => "users", "action" => "dashboard"));
        }
        
        $this->Session->setFlash(__("Your session could not be started."));
        $this->redirect(array("controller" => "pages", "action" => "error"));
    }       
    
    /** 
     * Inline call that syncs the Rdio discography of the preloaded Artist. 
     *
     * @return boolean True on success, false on failure
     */
    public function syncArtistDiscography()
    {           
        $data           = $this->Artist->RdioArtist->data;
        $artistId       = $data["Artist"]["id"];
        $rdioKey        = $data["RdioArtist"]["key"];                
        $albums         = $this->RdioApi->getAlbumsForArtist($rdioKey);
        
        if($albums)
        {
            $this->loadModel("Album");
            $this->Album->saveDiscography($artistId, $rdioKey, $albums);
            return $this->Artist->RdioArtist->setSyncTimestamp($data) !== false;
        }        
        return false;
    }
    
    /** 
     * Inline call that syncs the LastFm information of the preloaded Artist. 
     *
     * @return boolean True on success, false on failure
     */
    public function syncArtistDetails()
    {           
        $data           = $this->Artist->LastfmArtist->data;
        $artistName     = $data["Artist"]["name"];      
        $infos          = $this->LastfmApi->getArtistBiography($artistName);
                
        if($infos)
        {
            return $this->Artist->LastfmArtist->saveDetails($data, $infos) !== false;
        }        
        return false;
    }        
    
    /** 
     * Inline call that syncs the LastFm album popularity of the preloaded Artist. 
     *
     * @return boolean True on success, false on failure
     */
    public function syncArtistNotableAlbums()
    {           
        $data           = $this->Artist->LastfmArtist->data;
        $artistName     = $data["Artist"]["name"];  
        $infos          = $this->LastfmApi->getArtistTopAlbums($artistName);
                
        if($infos)
        {
            $this->loadModel("Album");
            return $this->Album->LastfmAlbum->saveNotableAlbums($data, $infos) !== false;
        }        
        return false;
    }    
    
    
}
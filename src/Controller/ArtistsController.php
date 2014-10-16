<?php
namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;

class ArtistsController extends AppController {

    public $components  = ["Paginator"];
    public $paginate    = ['limit' => 25];

    /**
     * Artist list landing page. Displays CTAs and insentives
     */
    public function index()
    {
        $popularArtists = $this->Artists->find('popular', ['limit' => 9])->toArray();
        $letters        = $this->Artists->find('firstLetters');
        $newReleases    = $this->Artists->Albums->find('newReleases', ['limit' => 8]);
        $featuredArtist = array_shift($popularArtists);

        $this->set([
            'featuredArtist'    => $featuredArtist,
            'popularArtists'    => $popularArtists,
            'artistCategories'  => $letters,
            'newReleases'       => $newReleases,
            "meta"  => [
                "title"         => __("Artist list"),
                "keywords"      => [__("artists reviews")],
                "description"   => __("Listing of all artists covered in The Music Tank's reviewing pool.")
            ]
        ]);
    }

    /**
     * Artist profile page.
     *
     * @param string $artistSlug Artist slug
     */
    public function view($artistSlug = "")
    {
        $artist = $this->Artists->find('slug', ['slug' => $artistSlug])->first();
        if(!$artist)
        {
            // query LastFm before 404-ing
            if (TableRegistry::get('Artists')->find('localOrRemote', ['criteria' => $query, 'limit' => 3])) {
                // if we saved something, assume it's loadable.
                $artist = $this->Artists->find('slug', ['slug' => $artistSlug])->first();
                if(!$artist) {
                    throw new NotFoundException(sprintf(__("Could not find the artist %s"), $artistSlug));
                }
            }
            else {
                throw new NotFoundException(sprintf(__("Could not find the artist %s"), $artistSlug));
            }
        }

        $this->set([
            'artist' => $artist,
            "meta"  => [
                "title"         => $artist->name,
                "oembedLink"    => $artist->getOEmbedUrl(),
                "keywords"      => [$artist->name, __("artists reviews")],
                "description"   => sprintf(__("Listening statistics of %s's discography."), $artist->name)
            ]
        ]);
    }

    /**
     * Artist discography page.
     *
     * @param string $artistSlug Artist slug
     */
    public function discography($artistSlug = "")
    {
        $artist = $this->Artists->find('slug', ['slug' => $artistSlug])->first();

        if (!$artist) {
            throw new NotFoundException(sprintf(__("Could not find the artist %s"), $artistSlug));
        }

        $this->set([
            'artist' => $artist,
            "meta"  => [
                "title"         => $artist->name,
                "oembedLink"    => $artist->getOEmbedUrl(),
                "keywords"      => [$artist->name, __("artists reviews")],
                "description"   => sprintf(__("%s's discography."), $artist->name)
            ]
        ]);
    }

    /**
     * Artist wiki page.
     *
     * @param string $artistSlug Artist slug
     */
    public function wiki($artistSlug = "")
    {
        $artist = $this->Artists->find('slug', ['slug' => $artistSlug])->first();

        if (!$artist) {
            throw new NotFoundException(sprintf(__("Could not find the artist %s"), $artistSlug));
        }

        $this->set([
            'artist' => $artist,
            "meta"  => [
                "title"         => $artist->name,
                "oembedLink"    => $artist->getOEmbedUrl(),
                "keywords"      => [$artist->name, __("artists reviews")],
                "description"   => sprintf(__("%s's wiki."), $artist->name)
            ]
        ]);
    }

    /**
     * Browse artists by letter.
     *
     * @param string $letter A supported letter
     */
    public function browse($letter = "a")
    {
        $letters    = $this->Artists->find('firstLetters');
        $searchStr  = trim($letter);
        $query      = $this->Artists->find('browse', ['letter' => $searchStr]);
        $results    = $this->Paginator->paginate($query);
        $title      = sprintf(__("Browsing: \"%s\""), $searchStr);

        $this->set([
            'artists'   => $results,
            'artistCategories'  => $letters,
            'title'     => $title,
            'letter'    => $letter
        ]);

        $this->render("search");
    }

    /**
     * Browse albums by term. Renders same view as browse action.
     */
    public function search()
    {
        $title = __("Search");
        $results = [];

        if(!is_null($this->request->query('name'))) {
            $searchStr = trim($this->request->query('name'));
            $query = $this->Artists->find('search', ['criteria' => $searchStr]);
            $results = $this->Paginator->paginate($query);
            $title = sprintf(__("Searching for: \"%s\""), $searchStr);
        }

        $this->set([
            "artists" => $results,
            "meta"  => [
                "title"         => $title,
                "keywords"      => [__("artist search")],
                "description"   => __("Search our artists database.")
            ]
        ]);
    }
}

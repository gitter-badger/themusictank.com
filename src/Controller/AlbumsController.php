<?php
namespace App\Controller;

use Cake\Network\Exception\NotFoundException;

use App\Controller\AppController;

class AlbumsController extends AppController {

    public $components  = ["Paginator"];

    /**
     * New albums page.
     */
    public function newReleases()
    {
        $weekDate       = date("F j Y", mktime(0, 0, 0, date("n"), date("j") - date("N")));
        $title          = sprintf(__("New releases for the week of %s"), $weekDate);
        $limit          = 4*4;
        $newReleases    = $this->Paginator->paginate($this->Album->getNewReleases($limit), ['limit' => $limit]);

        $this->set([
            'newReleases'   => $newReleases,
            "meta"  => [
                "title"         => __("New releases"),
                "keywords"      => [__("new album releases")],
                "description"   => __("Browse the list of the albums recently added on The Music Tank.")
            ]
        ]);
    }


      /**
     * Album profile page.
     * @param string $artistSlug Artist slug (unused)
     * @param string $albumSlug Album slug
     */
    public function view($albumSlug = "")
    {
        $album = $this->Albums->getBySlug($albumSlug)->first();

        if (!$album) {
            throw new NotFoundException(sprintf(__("Could not find the album %s"), $albumSlug));
        }

        if(!$album->lastfm || !$album->lastfm->hasSyncDate()) {
            $this->redirect(["controller" => "albums", "action" => "processing", $albumSlug]);
        }

        $this->set([
            "album" => $album,
            "meta"  => [
                "title"         => $album->getContextualNames(),
                "oembedLink"    => $album->getOEmbedUrl(),
                "keywords"      => array_merge($album->getContextualNames(), [__("album review")]),
                "description"   => sprintf(__("View the reviewing statistics of %s, an album by %s that was released %s."),
                                        $album->name, $album->artist->name, $album->getFormatedReleaseDate()
                                   )
            ]
        ]);
    }

    public function wiki($albumSlug = "")
    {
        $album = $this->Albums->getBySlug($albumSlug)->first();
        if (!$album) {
            throw new NotFoundException(sprintf(__("Could not find the album %s"), $albumSlug));
        }

        if (!$album->lastfm || !$album->lastfm->hasSyncDate()) {
            $this->redirect(["controller" => "albums", "action" => "processing", $albumSlug]);
        }

        $this->set([
            "album" => $album,
            "meta"  => [
                "title"         => $album->getContextualNames(),
                "oembedLink"    => $album->getOEmbedUrl(),
                "keywords"      => array_merge($album->getContextualNames(), [__("album review")]),
                "description"   => sprintf(__("View a description of %s, an album by %s that was released %s."),
                                        $album->name, $album->artist->name, $album->getFormatedReleaseDate()
                                   )
            ]
        ]);
    }

    public function processing($albumSlug = "")
    {
        $album = $this->Albums->getBySlug($albumSlug)->first();

        if (!$album) {
            throw new NotFoundException(sprintf(__("Could not find the album %s"), $albumSlug));
        }

        $this->set([
            "album" => $album,
            "meta"  => [
                "title"         => $album->getContextualNames(),
                "oembedLink"    => $album->getOEmbedUrl(),
                "keywords"      => array_merge($album->getContextualNames(), [__("album review")]),
                "description"   => sprintf(__("Processsing statistics for %s, an album by %s that was released %s."),
                                        $album->name, $album->artist->name, $album->getFormatedReleaseDate()
                                   )
            ]
        ]);
    }

    /**
     * Album profile page.
     * @param string $artistSlug Artist slug (unused)
     * @param string $albumSlug Album slug
     */
    public function embed($albumSlug = "")
    {
        $album = $this->Albums->getBySlug($albumSlug)->first();

        if(!$album) {
            throw new NotFoundException(sprintf(__("Could not find the album %s"), $albumSlug));
        }

        if (!$album->lastfm || !$album->lastfm->hasSyncDate()) {
            $this->redirect(["controller" => "albums", "action" => "processing", $albumSlug]);
        }

        $this->layout = "blank";
        $this->set([
            "album" => $album,
            "meta"  => [
                "title"         => $album->getContextualNames(),
                "oembedLink"    => $album->getOEmbedUrl(),
                "keywords"      => array_merge($album->getContextualNames(), [__("album review embed")]),
                "description"   => sprintf(__("View the reviewing statistics of %s, an album by %s that was released %s."),
                                        $album->name, $album->artist->name, $album->getFormatedReleaseDate()
                                   )
            ]
        ]);
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
            $query = $this->Album->searchCriteria($searchStr);
            $results = $this->Paginator->paginate($query);
            $title = sprintf(__("Searching for: \"%s\""), $searchStr);
        }

        $this->set([
            "albums" => $results,
            "meta"  => [
                "title"         => $title,
                "keywords"      => [__("album search")],
                "description"   => __("Search our albums database.")
            ]
        ]);
    }


}

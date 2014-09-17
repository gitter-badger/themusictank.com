<?php
namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;

class TracksController extends AppController {

    var $helpers    = ["Time", "Html"];
    var $components = ["Paginator"];

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->deny(["by_subscriptions"]);
    }

    /**
     * Track profile page.
     *
     * @param string $trackSlug Track slug
     */
    public function view($trackSlug = "")
    {
        $track = $this->Tracks->getBySlug($trackSlug)->first();

        if (!$track) {
            throw new NotFoundException(sprintf(__("Could not find the track %s"), $trackSlug));
        }

        $this->set([
            "track" => $track,
            "meta"  => [
                "title"         => $track->getContextualNames(),
                "oembedLink"    => $track->getOEmbedUrl(),
                "keywords"      => array_merge($track->getContextualNames(), [__("song review")]),
                "description"   => sprintf(__("Listening statistics of %s, a track featured on %s. An album by %s that was released on %s."),
                                        $track->title, $track->album->name, $track->album->artist->name, $track->album->getFormatedReleaseDate()
                                   )
            ]
        ]);
    }

    /**
     * Track wiki page.
     *
     * @param string $trackSlug Track slug
     */
    public function wiki($trackSlug = "")
    {
        $track = $this->Tracks->getBySlug($trackSlug)->first();

        if (!$track) {
            throw new NotFoundException(sprintf(__("Could not find the track %s"), $trackSlug));
        }

        $this->set([
            "track" => $track,
            "meta"  => [
                "title"         => $track->getContextualNames(),
                "oembedLink"    => $track->getOEmbedUrl(),
                "keywords"      => array_merge($track->getContextualNames(), [__("song review")]),
                "description"   => sprintf(__("Listening statistics of %s, a track featured on %s. An album by %s that was released on %s."),
                                        $track->title, $track->album->name, $track->album->artist->name, $track->album->getFormatedReleaseDate()
                                   )
            ]
        ]);
    }


    /**
     * Browse tracks by term.
     */
    public function search()
    {
        $title = __("Search");
        $results = [];

        if(!is_null($this->request->query('name'))) {
            $searchStr = trim($this->request->query('name'));
            $query = $this->Tracks->searchCriteria($searchStr);
            $results = $this->Paginator->paginate($query);
            $title = sprintf(__("Searching for: \"%s\""), $searchStr);
        }

        $this->set([
            "title"   => $title,
            "tracks" => $results,
            "meta"  => [
                "title"         => $title,
                "keywords"      => [__("track search")],
                "description"   => __("Search our tracks database.")
            ]
        ]);
    }

}
?>

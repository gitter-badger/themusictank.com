<?php
namespace App\Controller;

use App\Controller\AppController, Cake\Event\Event;

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
     * @param string $trackSlug Album slug
     */
    public function view($trackSlug = "")
    {
        $track = $this->Track->getBySlug($trackSlug)->first();

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

}
?>

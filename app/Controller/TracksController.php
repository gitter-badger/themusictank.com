<?php
class TracksController extends AppController {

    var $helpers    = array("Chart", "Time");
    var $components = array("Paginator");
    var $paginate = array('limit' => 25);

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->deny(array("by_subscriptions"));
    }

    /**
     * Track profile page.
     *
     * @param string $artistSlug Artist slug
     * @param string $albumSlug Artist slug
     * @param string $trackSlug Album slug
     */
    public function view($trackSlug)
    {
        $this->usesPlayer();

        $isLoggedIn = $this->userIsLoggedIn();
        $data       = $this->Track->getUpdatedSetBySlug($trackSlug, $isLoggedIn);
        if(!$data) throw new NotFoundException(sprintf(__("Could not find the track %s"), $trackSlug));

        // Set the default track information.
        $this->set("album", 	$data["Album"]);
        $this->set("artist", 	$data["Album"]["Artist"]);
        $this->set("track", 	$data["Track"]);
        $this->set("rdioTrack", $data["RdioTrack"]);
        $this->set("lastfmTrack", $data["LastfmTrack"]);

        // Load the users who have reviewed the track
        $this->set("usersWhoReviewed", $this->User->getRecentTrackReviewers($data["Track"]["id"]));

        // Load the previous and next tracks
        $this->set("nextTrack", $this->Track->getNextTrack());
        // The object was reset during the previous query.
        $this->Track->track_num = $data["Track"]["track_num"];
        $this->set("previousTrack", $this->Track->getPreviousTrack());

        // Load the review snapshots
		$this->set("trackReviewSnapshot", Hash::extract($this->Track->getSnapshot(), "TrackReviewSnapshot"));
        if($isLoggedIn) {
			$this->set("userTrackReviewSnapshot", Hash::extract($this->Track->getUserSnapshot($this->getAuthUserId()), "UserTrackReviewSnapshot"));
			$this->set("subsTrackReviewSnapshot", Hash::extract($this->Track->getUserSubscribersSnapshot($this->getAuthUserId()), "UserTrackReviewSnapshot"));
            $this->set("subsWhoReviewed", $this->User->getSubscribersWhichReviewedTrack($this->getAuthUserId(), $data["Track"]["id"]));
        }

        $this->set("oembedLink", $this->Track->getOEmbedUrl());

        $this->setPageTitle(array($data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]));
        $this->setPageMeta(array(
            "keywords" => array($data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]),
            "description" => sprintf(
                __("Listening statistics of %s, a track featured on %s. An album by %s that was released on %s."),
                $data["Track"]["title"],
                $data["Album"]["name"],
                $data["Album"]["Artist"]["name"],
                date("F j Y", $data["Album"]["release_date"])
             )
        ));
    }

    /**
     * Album profile page.
     * @param string $artistSlug Artist slug (unused)
     * @param string $trackSlug Album slug
     */
    public function embed($trackSlug)
    {
        $this->layout = "blank";

        $data = $this->Track->getUpdatedSetBySlug($trackSlug);
        if(!$data) throw new NotFoundException(sprintf(__("Could not find the track %s"), $trackSlug));

        $this->set("track", 		$data["Track"]);
        $this->set("rdioTrack", 	$data["RdioTrack"]);
        $this->set("lastfmTrack", 	$data["LastfmTrack"]);
        $this->set("album", 		$data["Album"]);
        $this->set("artist", 		$data["Album"]["Artist"]);
        $this->set("trackReviewSnapshot", $data["TrackReviewSnapshot"]);
        $this->set("oembedLink", 	$this->Track->getOEmbedUrl());
    }

    public function by_user($trackSlug, $userSlug)
    {
        $userData = $this->User->findBySlug($userSlug, array("fields" => "User.*"));
        if(!$userData) throw new NotFoundException(sprintf(__("Could not find the user %s"), $userSlug));

        $isLoggedIn = $this->userIsLoggedIn();

        $data = $this->Track->getUpdatedSetBySlug($trackSlug, $isLoggedIn);
        if(!$data) throw new NotFoundException(sprintf(__("Could not find the track %s"), $trackSlug));

        $this->usesPlayer();

        // Setup the basic variables
        $this->set("viewingUser", 	$userData["User"]);
        $this->set("track", 		$data["Track"]);
        $this->set("rdioTrack", 	$data["RdioTrack"]);
        $this->set("lastfmTrack", 	$data["LastfmTrack"]);
        $this->set("album", 		$data["Album"]);
        $this->set("artist", 		$data["Album"]["Artist"]);

 		// Load the review snapshots
		$this->set("trackReviewSnapshot", Hash::extract($this->Track->getSnapshot(), "TrackReviewSnapshot"));
		// Only load the profile snapshot if the profile is not the current user
		if(!$isLoggedIn || $this->Session->read('Auth.User.User.id') != $userData["User"]["id"]) {
			$this->set("profileTrackReviewSnapshot", Hash::extract($this->Track->getUserSnapshot($userData["User"]["id"]), "UserTrackReviewSnapshot"));
		}
        if($isLoggedIn) {
			$this->set("userTrackReviewSnapshot", Hash::extract($this->Track->getUserSnapshot($this->getAuthUserId()), "UserTrackReviewSnapshot"));
        }

        $this->setPageTitle(array($data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]));
        $this->setPageMeta(array(
            "keywords" => array($data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]),
            "description" => sprintf(
                __("Listening statistics of %s, a track featured on %s. An album by %s that was released on %s."),
                $data["Track"]["title"],
                $data["Album"]["name"],
                $data["Album"]["Artist"]["name"],
                date("F j Y", $data["Album"]["release_date"])
             )
        ));
    }

    public function by_subscriptions($trackSlug)
    {
        $data = $this->Track->getBySlugContained($trackSlug);
        if(!$data) throw new NotFoundException(sprintf(__("Could not find the track %s"), $trackSlug));

        $this->set("track", 	$data["Track"]);
        $this->set("album", 	$data["Album"]);
        $this->set("artist", 	$data["Album"]["Artist"]);
        $this->set("usersWhoReviewed", $this->User->getSubscribersWhichReviewedTrack($this->getAuthUserId(), $data["Track"]["id"]));

        $this->setPageTitle(array($data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]));
        $this->setPageMeta(array(
            "keywords" => array($data["Track"]["title"], $data["Album"]["name"], $data["Album"]["Artist"]["name"]),
            "description" => sprintf(
                __("Listening statistics of %s, a track featured on %s. An album by %s that was released on %s."),
                $data["Track"]["title"],
                $data["Album"]["name"],
                $data["Album"]["Artist"]["name"],
                date("F j Y", $data["Album"]["release_date"])
             )
        ));
    }

    /**
     * Browse albums by term. Renders same view as browse action.
     */
    public function search()
    {
        if($this->request->is('get'))
        {
            $this->set('tracks', $this->Paginator->paginate('Track', array('Track.title LIKE' => "%". trim($this->request->query['name'])."%")));
            $title = sprintf(__("Searching for: \"%s\""), trim($this->request->query['name']));
        }
        else
        {
            $title = __("Search");
        }

        $this->set("title", $title);
        $this->setPageTitle(array($title, __("Album list")));
        $this->setPageMeta(array(
            "description" => __("Search page")
        ));
    }
}

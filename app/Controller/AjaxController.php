<?php
/**
 * AlbumsController controller
 *
 * Contains Album pages methods
 *
 * @package       app.Controller
 */

class AjaxController extends AppController {

    public function beforeFilter()
    {
        $this->layout   = "ajax";
        parent::beforeFilter();
        $this->Auth->deny("whatsup", "okstfu", "follow", "unfollow", "pushrf");
    }

    /**
     * Json call that lists recent Notifications for the current user
     */
    public function whatsup()
    {
        $this->set("notifications", $this->User->Notifications->findByUserId($this->getAuthUserId(), 5));
        $this->render('dropdownnotifications');
    }

    /**
     * Json call that changes the status of notifications to 'read'
     */
    public function okstfu()
    {
        $this->User->Notifications->markAsRead(time());
        $this->set("notifications", $this->User->Notifications->findByUserId($this->getAuthUserId(), 5));
        $this->render('dropdownnotifications');
    }

    public function follow($userSlug)
    {
        $relationExists = $this->userIsLoggedIn() && $this->User->UserFollowers->addRelation($this->getAuthUserId(), $userSlug);
        $this->set("user", array("slug" => $userSlug, "currently_followed" => $relationExists));
        $this->render("followbutton");
    }

    public function unfollow($userSlug)
    {
        $relationExists = $this->userIsLoggedIn() && !$this->User->UserFollowers->removeRelation($this->getAuthUserId(), $userSlug);
        $this->set("user", array("slug" => $userSlug, "currently_followed" => $relationExists));
        $this->render("followbutton");
    }

    public function bugreport()
    {
		if ($this->request->is('post'))
		{
	    	$this->loadModel("Bug");

	    	$postData = $this->request->data;
	    	$notificationData = null;
	    	$bug = new Bug();

	    	if(Hash::check($postData, "id")) {
	    		$bug->updateReport((int)Hash::get($postData, "id"), Hash::get($postData, "details"));
	    	}
	    	else {
	    		$bugId = $bug->createReport(Hash::get($postData, "type"), Hash::get($postData, "where"), (int)Hash::get($postData, "user_id"));
	    		$this->set("bugId", $bugId);
	    	}
        	$this->render("bugreport");
	    }
    }

    public function oembed()
    {
        $this->response->type('application/json');

        if(!array_key_exists("url", $this->request->query))
        {
            throw new NotFoundException();
        }

        $instance = $this->_loadObjectFromOEmbededUrl($this->request->query["url"]);

        if(!$instance->data)
        {
            throw new NotFoundException();
        }

        $this->set("jsonOutput", $instance->toOEmbed());
        $this->render('index');
    }

    /**
     * Push review frames while reviewing
     */
    public function pushrf($keys, $shaCheck)
    {
        $this->response->type('application/json');

        $keyMapping = explode("-", $keys);
        $userId = $this->getAuthUserId();
        $validSha =  sha1($userId . (int)$keyMapping[2] . "user is reviewing something kewl");

        if($userId !== (int)$keyMapping[3])
        {
            throw new NotFoundException(__("This is not your user."));
        }
        elseif(!array_key_exists("frames", $this->request->data))
        {
            throw new NotFoundException(__("Request is missing frames."));
        }
        elseif($shaCheck == $validSha)
        {
            $this->loadModel("ReviewFrame");
            $this->set("jsonOutput", array("status" => $this->ReviewFrame->savePlayerData($this->request->data["frames"], $keyMapping) ? "success" : "failure"));
        }
        else
        {
            throw new NotFoundException(__("We don't know where you are from."));
        }
        $this->render('index');
    }

    public function getdiscography($artistSlug)
    {
        $this->loadModel("Artist");
        $this->loadModel("Album");
        $this->Album->data = $this->Artist->findBySlug($artistSlug);

        if (count($this->Album->data))
        {
            $albums = $this->Album->updateDiscography($this->Album->data["Artist"]["name"]);
            $this->set("albums", $albums);
        }
        else throw new NotFoundException(__("We cannot load this artist."));
    }

    public function getsong($artistSlug, $trackSlug)
    {
        $this->response->type('application/json');
        App::uses('HttpSocket', 'Network/Http');
        $this->loadModel("Artist");
        $this->loadModel("Track");

        $artist = $this->Artist->findBySlug($artistSlug);
        $track = $this->Track->findBySlug($trackSlug);

        if(!$artist || !$track)
            throw new NotFoundException(__("We don't know where you are from."));


        $cacheName = 'youtube-' . Hash::get($artist, "Artist.name") . '-' . Hash::get($track, "Track.title");
        $result = Cache::read($cacheName, "weekly");
        if (!$result) {
            $HttpSocket = new HttpSocket();
       		$response = $HttpSocket->get('http://gdata.youtube.com/feeds/api/videos', array(
	            "alt" => "json",
	            "max-results" => 1,
	            "q" => Hash::get($artist, "Artist.name") . "-" . Hash::get($track, "Track.title")
	        ));

	        if($response->isOk()) {
	        	$result = $response->body();
            	Cache::write($cacheName, $result, "weekly");
	        }
        }
       	$this->set("jsonOutput", json_decode($result));

       $this->render('index');
    }

    public function savewave($trackSlug, $shaCheck)
    {
        $this->response->type('application/json');

        $this->loadModel("Track");
        $data = $this->Track->findBySlug($trackSlug);

        if(!$data)
        {
            throw new NotFoundException();
        }

        $validSha = sha1($data["Track"]["slug"] . $data["Track"]["id"] . "foraiurtheplayer");
        if($shaCheck != $validSha)
        {
            throw new NotFoundException(__("We don't know where you are from."));
        }

        $this->Track->data = $data;
        $this->set("jsonOutput", $this->Track->saveWave($this->request->data["waves"]));
        $this->render('index');
    }

    public function artistsSearch()
    {
        $this->loadModel("Artist");

        $results    = array();
        $query      = trim($this->request->query['q']);
        $artists    = $this->Artist->search($query, 3);

        if(count($artists) > 0)
        {
            $labels = Hash::extract($artists, '{n}.Artist.name');
            $slugs  = Hash::extract($artists, '{n}.Artist.slug');

            foreach($labels as $i => $row)
            {
                $results[] = array(
                    "slug"  => $slugs[$i],
                    "artist" => $labels[$i]
                );
            }
        }

        $this->set("jsonOutput", $results);
        $this->render('index');
    }

    public function albumsSearch()
    {
        $this->loadModel("Album");

        $results    = array();
        $query = trim($this->request->query['q']);
        $albums = $this->Album->search($query, 3);

        if(count($albums) > 0)
        {
            $labels = Hash::extract($albums, '{n}.Album.name');
            $slugs  = Hash::extract($albums, '{n}.Album.slug');
            $artists= Hash::extract($albums, '{n}.Artist.name');

            foreach($labels as $i => $row)
            {
                $results[] = array(
                    "slug"  => $slugs[$i],
                    "album" => $labels[$i],
                    "artist" => $artists[$i]
                );
            }
        }

        $this->set("jsonOutput", $results);
        $this->render('index');
    }

    public function tracksSearch()
    {
        $this->loadModel("Track");

        $results    = array();
        $query      = trim($this->request->query['q']);
        $tracks     = $this->Track->search($query, 3);

        if(count($tracks) > 0)
        {
            $labels         = Hash::extract($tracks, '{n}.Track.title');
            $slugs          = Hash::extract($tracks, '{n}.Track.slug');
            $albums         = Hash::extract($tracks, '{n}.Album.name');
            $artists        = Hash::extract($tracks, '{n}.Artist.name');

            foreach($labels as $i => $row)
            {
                $results[] = array(
                    "track" => $labels[$i],
                    "slug"  => $slugs[$i],
                    "album" => $albums[$i],
                    //"artist" => $artists[$i]
                );
            }
        }

        $this->set("jsonOutput", $results);
        $this->render('index');
    }

    private function _loadObjectFromOEmbededUrl($url)
    {
        $pattern = explode("/", preg_replace('/http:\/\//', "", $url));

        if(count($pattern) < 3 && count($pattern) > 4)
        {
            throw new NotFoundException();
        }

        $model = $pattern[1];
        $slug = $pattern[3];

        if(!preg_match('/albums|tracks/i', $model))
        {
            throw new NotFoundException();
        }

        $modelName = substr(ucfirst($model), 0, -1);
        $this->loadModel($modelName);

        $instance = new $modelName();
        $instance->getUpdatedSetBySlug($slug);

        return $instance;
    }

}

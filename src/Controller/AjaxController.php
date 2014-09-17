<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\Bug;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Network\Exception\NotFoundException;

class AjaxController extends AppController {

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->deny("whatsup", "okstfu", "follow", "unfollow", "pushrf");
        $this->layout   = "ajax";
    }

    /**
     * Json call that lists recent Notifications for the current user
     */
    public function whatsup()
    {
        $user = $this->getAuthUser();
        $notifications = TableRegistry::get('Notifications')->findByUserId($user->id)->limit(5)->order(["created" => "DESC"])->all();

        $this->set("notifications", $notifications);
        $this->render('whatsup');
    }

    /**
     * Json call that changes the status of notifications to 'read'
     */
    public function okstfu()
    {
        TableRegistry::get('Notifications')->markAsRead($this->getAuthUser(), time());
        $this->redirect(['controller' => 'ajax', 'action' => 'whatsup']);
    }

    public function getsong($trackSlug)
    {
        $track = TableRegistry::get('Tracks')->getBySlug($trackSlug)->first();

        if(is_null($track)) {
            throw new NotFoundException(__("We cannot load this track."));
        }

        $track->fetchSong();
        $this->response->type('application/json');
        $this->set("jsonOutput", ["vid" => $track->youtube->youtube_key]);

       $this->render('index');
    }

    public function getdiscography($artistSlug)
    {
        $artist = TableRegistry::get('Artists')->getBySlug($artistSlug)->first();

        if (is_null($artist)) {
            throw new NotFoundException(__("We cannot load this artist."));
        }

        $artist->fetchDiscography();
        $this->set('artist', $artist);
    }

    public function artistsSearch()
    {
        $results    = array();
        $query      = trim($this->request->query['q']);
        $resultSet  = TableRegistry::get('Artists')->searchCriteria($query, 3)->all();

        foreach($resultSet as $artist) {
            $results[] = [
                "slug"      => $artist->slug,
                "artist"    => $artist->name
            ];
        }

        $this->response->type('application/json');
        $this->set("jsonOutput", $results);
        $this->render('index');
    }

    public function albumsSearch()
    {
        $results    = array();
        $query      = trim($this->request->query['q']);
        $resultSet  = TableRegistry::get('Albums')->searchCriteria($query, 3);

        foreach($resultSet as $album) {
            $results[] = [
                "slug"      => $album->slug,
                "album"     => $album->name,
                "artist"    => $album->artist->name
            ];
        }

        $this->response->type('application/json');
        $this->set("jsonOutput", $results);
        $this->render('index');
    }

    public function tracksSearch()
    {
        $results    = array();
        $query      = trim($this->request->query['q']);
        $resultSet  = TableRegistry::get('Tracks')->searchCriteria($query, 3);

        foreach($resultSet as $track) {
            $results[] = [
                "slug"      => $track->slug,
                "track"     => $track->title,
                "album"     => $track->album->name,
                //"artist"    => $track->album->artist->name
            ];
        }

        $this->response->type('application/json');
        $this->set("jsonOutput", $results);
        $this->render('index');
    }

    public function bugreport()
    {
        if ($this->request->is('post'))
        {
            $BugsTable = TableRegistry::get('Bugs');
            $bug = $BugsTable->newEntity($this->request->data);

            if ($BugsTable->save($bug)) {
                $this->set("bug", $bug);
            }
            $this->render("bugreport");
        }
    }


// to be refactored ->





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


}

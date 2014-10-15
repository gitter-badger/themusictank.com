<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;

use App\Model\Entity\LastfmTrack;
use App\Model\Entity\OembedableTrait;
use App\Model\Entity\TrackYoutube;
use App\Model\Api\YoutubeApi;

class Track extends Entity
{
    use OembedableTrait;

    // @todo : There should be a review date field availlable here
    // to allow sorting
    public function getRecentReviewers($limit = 5)
    {
        $usersId = TableRegistry::get('UserTrackReviewSnapshots')
            ->getByTrackId($this->id)
            ->select(['user_id'])
            ->limit((int)$limit);

        return TableRegistry::get('Users')->getByIds($usersId->toArray())->limit((int)$limit);
    }

    // @todo : There should be a review date field availlable here
    // to allow sorting
    public function getRecentSubscriptionsReviewers($userId, $limit = 5)
    {
        $usersId = TableRegistry::get('SubscriptionTracksReviewSnapshots')
            ->getByTrackIdAndUserId($this->id, $userId)
            ->select(['user_id'])
            ->limit((int)$limit)->all();

        return TableRegistry::get('Users')->getByIds($usersId->toArray())->limit((int)$limit);
    }

    public function getIntroduction()
    {
        if (!is_null($this->lastfm->wiki_curated)) {
            return $this->lastfm->wiki_curated;
        }
        else if (!is_null($this->lastfm->wiki)) {
            return $this->lastfm->wiki;
        }

        $albumUrl = Router::url(['controller' => 'albums', 'action' => 'view', $this->album->slug]);
        $artistUrl = Router::url(['controller' => 'artists', 'action' => 'view', $this->album->artist->slug]);
        $linkPattern = '<a href="%s" alt="%s">%s</a>';
        $albumLink =  sprintf($linkPattern, $albumUrl, h($this->album->name), h($this->album->name));
        $artistLink =  sprintf($linkPattern, $artistUrl, h($this->album->artist->name), h($this->album->artist->name));

        $trackIdxStr = $this->track_num . "<sup>th</sup>";
        if((int)$this->track_num === 3) {
            $trackIdxStr = "third";
        }
        elseif((int)$this->track_num === 2) {
            $trackIdxStr = "second";
        }
        elseif((int)$this->track_num === 1) {
            $trackIdxStr = "first";
        }

        return sprintf(__("This is the %s track off of %s. The album by %s that has been released on %s."), $trackIdxStr, $albumLink, $artistLink, $this->album->getFormatedReleaseDate());
    }

    public function getPlayerAttributes()
    {
        if(!is_null($this->youtube)) {
            if(!is_null($this->youtube->youtube_key_manual)) {
                return sprintf('data-song-vid="%s"', $this->youtube->youtube_key_manual);
            }
            elseif(!is_null($this->youtube->youtube_key)) {
                return sprintf('data-song-vid="%s"', $this->youtube->youtube_key);
            }
        }
        return sprintf('data-song="%s"', $this->slug);
    }

    public function getContextualNames()
    {
        return [$this->title, $this->album->name, $this->album->artist->name];
    }

    public function getBestArea()
    {
        return null;
    }

    public function getWorstArea()
    {
        return null;
    }

    public function fetchSong()
    {
        // Since this is updated in a  just in time
        // matter, the youtube property could be null
        // the first time the track get loaded.
        if(is_null($this->youtube)) {
            $this->youtube = new TrackYoutube();
        }

        if($this->youtube->isExpired()) {
            $youtubeApi = new YoutubeApi();
            $videoId = $youtubeApi->getVideoId($this);

            // Regardless if there were additional albums added to this entity,
            // we have to save the last sync timestamp.
            $this->youtube->lastsync = time();
            $this->youtube->youtube_key = $videoId;
            $this->youtube->track_id = $this->id;

            return TableRegistry::get('TrackYoutubes')->save($this->youtube);
        }
        return false;
    }

    public function loadFromLastFm($trackInfo)
    {

        $this->title = trim($trackInfo['name']);
        $this->duration = (int)$trackInfo['duration'];
        $this->position = (int)$trackInfo['@attr']['rank'];

        // Save other secondary track information
        if (is_null($this->lastfm)) {
            $this->lastfm = new LastfmTrack();
        }
        $this->lastfm->loadFromLastFm($trackInfo);

        return $this;
    }

}

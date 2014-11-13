<?php

namespace App\Model\Entity;

use App\Model\Api\LastfmApi;
use App\Model\Entity\Track;
use App\Model\Entity\ThumbnailTrait;
use App\Model\Entity\OembedableTrait;

use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\ORM\Entity;
use Cake\Utility\Hash;

class Album extends Entity
{
    use ThumbnailTrait;
    use OembedableTrait;

    public function hasReleaseDate()
    {
        if (!is_null($this->get("release_date"))) {
            return $this->get("release_date")->toUnixString() > 0;
        }

        return false;
    }

    public function assignUniqueSlug()
    {
        if(!is_null($this->get("name"))) {
            $slug = TableRegistry::get("Albums")->find('uniqueSlug', ['slug' => $this->get("name")]);
            $this->set('slug', $slug);
            return true;
        }
        return false;
    }

    public function getTrackDuration()
    {
        $total = 0;

        foreach ($this->tracks as $track) {
            $total += $track->duration;
        }

        return $total;
    }

    public function getFormatedReleaseDate()
    {
        if (!is_null($this->release_date)) {
            return $this->release_date->timeAgoInWords();
        }

        return "never actually.";
    }

    public function isNotable()
    {
        return (int)$this->notability < 3;
    }

    public function getContextualNames()
    {
        return [$this->name, $this->artist->name];
    }

    /**
     * Compare this version of the entity with the one passed in parameter.
     * The one in parameter is considered the new copy; 'this' is the one from the DB.
     */
    public function compareData(array $albumInfo)
    {
        $name = trim(Hash::get($albumInfo, "name"));
        if (!empty($name) && $this->get("name") !== $name) {
            $this->set("name", $name);
        }

        $releaseDate = trim(Hash::get($albumInfo, "releasedate"));
        if (!empty($releaseDate) && $this->get("release_date_text") !== $releaseDate) {
            $this->set("release_date_text", $releaseDate);
            $this->set("release_date", new Time($releaseDate));
        }

        $thumbnail = Hash::check($albumInfo, 'image') ? $albumInfo['image'][3]['#text'] : "";
        if (!empty($thumbnail) && $this->get("image_src") !== $thumbnail) {
            $this->set("image_src", $thumbnail);
        }

        if (is_null($this->lastfm)) {
            $this->lastfm = new LastfmAlbum();
        }
        $this->lastfm->compareData($albumInfo);

        return $this;
    }

    public function loadFromLastFm($artistInfo)
    {
        // Loading against null values will just populate the entity.
        return $this->compareData($artistInfo);
    }

    // @todo : There should be a review date field availlable here
    // to allow sorting
    public function getRecentReviewers($limit = 5)
    {
        $usersId = TableRegistry::get('UserAlbumReviewSnapshots')
            ->getByAlbumId($this->id)
            ->select(['user_id'])
            ->limit((int)$limit)->all();

        return TableRegistry::get('Users')->getByIds($usersId->toArray())->limit((int)$limit);
    }

    // @todo : There should be a review date field availlable here
    // to allow sorting
    public function getRecentSubscriptionsReviewers($userId, $limit = 5)
    {
        $usersId = TableRegistry::get('SubscriptionAlbumReviewSnapshots')
            ->getByAlbumIdAndUserId($this->id, $userId)
            ->select(['user_id'])
            ->limit((int)$limit)->all();

        return TableRegistry::get('Users')->getByIds($usersId->toArray())->limit((int)$limit);
    }

    public function getBestTrack()
    {
        return TableRegistry::get('Tracks')
            ->getByAlbumId($this->id)
            ->order(['TrackReviewSnapshots.score' => 'DESC'])
            ->first();
    }

    public function getWorstTrack()
    {
        return TableRegistry::get('Tracks')
            ->getByAlbumId($this->id)
            ->order(['TrackReviewSnapshots.score' => 'ASC'])
            ->first();
    }

    public function syncToRemote()
    {
        $lastfmApi = new LastfmApi();
        $this->loadFromLastFm($lastfmApi->getAlbumInfo($this));
        TableRegistry::get('Albums')->save($this);

        $this->lastfm->modified = new \DateTime();
        TableRegistry::get('LastfmAlbums')->save($this->lastfm);
    }

    // Collect references to the track's mbid to lookup more quickly
    public function getMBIDList()
    {
        $map = [];
        foreach($this->tracks as $track) {
            $map[$track->mbid] = $track;
        }
        return $map;
    }

}

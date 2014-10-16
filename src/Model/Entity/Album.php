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

/*
    protected function _getIntroduction($title)
    {
        if (!is_null($this->get("lastfm")->get("wiki_curated"))) {
            return $this->get("lastfm")->get("wiki_curated");
        }
        elseif (!is_null($this->get("lastfm")->get("wiki"))) {
            return $this->get("lastfm")->get("wiki");
        }

        return sprintf(__("This is an album by %s."), $this->artist->name);
    }

    protected function _getIsProcessing()
    {
        return !($this->get("lastfm") && $this->get("lastfm")->hasSyncDate());
    }*/

    public function hasReleaseDate()
    {
        if (!is_null($this->get("release_date"))) {
            return $this->get("release_date")->toUnixString() > 0;
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


    public function loadFromLastFm($albumInfo)
    {
        $this->name = $albumInfo["name"];
        $this->release_date_text = trim($albumInfo["releasedate"]);
        $this->release_date = new Time($this->release_date_text);

        // Try and save a tuumbnail
        if (!empty($albumInfo['image'][3]['#text'])) {
            $this->image_src = $albumInfo['image'][3]['#text'];
        }

        // Save other secondary album information
        if (is_null($this->lastfm)) {
            $this->lastfm = new LastfmAlbum();
        }
        $this->lastfm->loadFromLastFm($albumInfo);

        // Save tracks
        $trackInfos = Hash::extract($albumInfo, "tracks.track");
        if (count($trackInfos)) {
            // Update matching tracks and append new ones if they don't match
            $mbids = $this->getMBIDList();
            foreach ($trackInfos as $trackInfo) {
                if(is_array($trackInfo) && array_key_exists('mbid', $trackInfo) && empty($trackInfo["mbid"]) != "") {
                    if(array_key_exists($trackInfo['mbid'], $mbids)) {
                        $mbids[$trackInfo['mbid']]->loadFromLastFm($trackInfo);
                    }
                    else {
                        $track = new Track();
                        $track->loadFromLastFm($trackInfo);
                        $this->tracks[] = $track;
                    }
                }
            }
        }

        return $this;
    }

}

<?php

namespace App\Model\Entity;

use Cake\ORM\Entity, App\Model\Entity\ThumbnailTrait, Cake\ORM\TableRegistry;

use App\Model\Entity\OembedableTrait;

class Album extends Entity
{
    use ThumbnailTrait;
    use OembedableTrait;

    public function getIntroduction()
    {
        if (!is_null($this->lastfm->wiki_curated)) {
            return $this->lastfm->wiki_curated;
        }
        else if (!is_null($this->lastfm->wiki)) {
            return $this->lastfm->wiki;
        }

        return sprintf(__("This is an album by %s."), $this->artist->name);
    }

    public function hasReleaseDate()
    {
        return (int)$this->release_date > 0;
    }

    public function getTrackDuration()
    {
        $total = 0;

        foreach ($this->tracks as $track) {
            $total += $track->duration;
        }

        return $total;
    }

    public function getFormatedReleaseDate($datePattern = "F j Y")
    {
        return date($datePattern, (int)$this->release_date);
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

}

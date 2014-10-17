<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

use App\Model\Api\LastfmApi;

use App\Model\Entity\LastfmArtist;
use App\Model\Entity\SyncTrait;
use App\Model\Entity\OembedableTrait;
use App\Model\Entity\ThumbnailTrait;

class Artist extends Entity {

    use OembedableTrait;
    use SyncTrait;
    use ThumbnailTrait;

    public function hasSnapshot()
    {
        return !is_null($this->snapshot) && $this->snapshot->isAvailable();
    }

    protected function _getIntroduction()
    {
        if (!is_null($this->get("lastfm")->get("biography_curated"))) {
            return $this->get("lastfm")->get("biography_curated");
        }
        elseif (!is_null($this->get("lastfm")->get("biography"))) {
            return $this->get("lastfm")->get("biography");
        }

        return "N/A";
    }

    public function getBestAlbum()
    {
        return TableRegistry::get('Albums')
            ->getByArtistId($this->id)
            ->order(['AlbumReviewSnapshots.score' => 'DESC'])
            ->first();
    }

    public function getWorstAlbum()
    {
        return TableRegistry::get('Albums')
            ->getByArtistId($this->id)
            ->order(['AlbumReviewSnapshots.score' => 'ASC'])
            ->first();
    }

    public function getBestTrack()
    {
        return TableRegistry::get('Tracks')
            ->getByAlbumIds(TableRegistry::get('Albums')->getIdsByArtist($this->id))
            ->order(['TrackReviewSnapshots.score' => 'DESC'])
            ->first();
    }

    public function getWorstTrack()
    {
        return TableRegistry::get('Tracks')
            ->getByAlbumIds(TableRegistry::get('Albums')->getIdsByArtist($this->id))
            ->order(['TrackReviewSnapshots.score' => 'ASC'])
            ->first();
    }

    /**
     * Compare this version of the entity with the one passed in parameter.
     * The one in parameter is considered the new copy; 'this' is the one from the DB.
     */
    public function compareData(array $artistInfo)
    {
        $name = trim(Hash::get($artistInfo, "name"));
        if (!empty($name) && $this->get("name") !== $name) {
            $this->set("name", $name);
        }

        $thumbnail = Hash::check($artistInfo, 'image') ? $artistInfo['image'][3]['#text'] : "";
        if (!empty($thumbnail) && $this->get("image_src") !== $thumbnail) {
            $this->set("image_src", $thumbnail);
        }

        if (is_null($this->lastfm)) {
            $this->lastfm = new LastfmArtist();
        }

        $this->lastfm->compareData($artistInfo);

        return $this;
    }

    public function loadFromLastFm($artistInfo)
    {
        // Loading against null values will just populate the entity.
        return $this->compareData($artistInfo);
    }
}

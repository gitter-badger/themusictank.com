<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

use App\Model\Api\LastfmApi;

use App\Model\Entity\SyncTrait;
use App\Model\Entity\OembedableTrait;

class Artist extends Entity {

    use OembedableTrait;
    use SyncTrait;

    public function getIntroduction()
    {
        if (!is_null($this->lastfm->biography_curated)) {
            return trim($this->lastfm->biography_curated);
        }
        else if (!is_null($this->lastfm->biography)) {
            return trim($this->lastfm->biography);
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


    public function fetchDiscography()
    {
        if($this->isExpired()) {
            $lastfmApi = new LastfmApi();
            $apiAlbums = $lastfmApi->getArtistTopAlbums($this);
            TableRegistry::get('LastfmAlbums')->filterNewByArtist($this, $apiAlbums);

            // Regardless if there were additional albums added to this entity,
            // we have to save the last sync timestamp.
            $this->lastsync = time();
            return TableRegistry::get('Artists')->save($this);
        }
        return false;
    }

}

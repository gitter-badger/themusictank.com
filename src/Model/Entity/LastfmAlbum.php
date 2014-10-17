<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Api\LastfmApi;
use App\Model\Entity\SyncTrait;

class LastfmAlbum extends Entity
{
    use SyncTrait;

    /**
     * Compare this version of the entity with the one passed in parameter.
     * The one in parameter is considered the new copy; 'this' is the one from the DB.
     */
    public function compareData(array $albumInfo)
    {
        $mbid = trim(Hash::get($artistInfo, "mbid"));
        if (!empty($mbid) && $this->get("mbid") !== $mbid) {
            $this->set("mbid", $mbid);
        }

        $url = trim(Hash::get($artistInfo, "url"));
        if (!empty($url) && $this->get("url") !== $url) {
            $this->set("url", $url);
        }

        $biography = Hash::check($artistInfo, 'wiki') ? trim($artistInfo['wiki']['summary']) : '';
        if (!empty($biography)) {
            $biography = LastfmApi::cleanWikiText($artistInfo['wiki']['summary']);
            if ($this->get("wiki") !== $biography) {
                $this->set("wiki", $biography);
            }
        }

        return $this;
    }

    public function loadFromLastFm(array $artistInfo)
    {
        // Loading against null values will just populate the entity.
        return $this->compareData($artistInfo);
    }
}

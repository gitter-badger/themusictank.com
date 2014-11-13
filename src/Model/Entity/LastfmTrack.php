<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Api\LastfmApi;
use App\Model\Entity\SyncTrait;
use Cake\Utility\Hash;

class LastfmTrack extends Entity
{
    use SyncTrait;

    /**
     * Compare this version of the entity with the one passed in parameter.
     * The one in parameter is considered the new copy; 'this' is the one from the DB.
     */
    public function compareData(array $trackInfo)
    {
        $mbid = trim(Hash::get($trackInfo, "mbid"));
        if (!empty($mbid) && $this->get("mbid") !== $mbid) {
            $this->set("mbid", $mbid);
        }

        $url = trim(Hash::get($trackInfo, "url"));
        if (!empty($url) && $this->get("url") !== $url) {
            $this->set("url", $url);
        }

        $biography = Hash::check($trackInfo, 'wiki') ? trim($trackInfo['wiki']['summary']) : '';
        if (!empty($biography)) {
            $biography = LastfmApi::cleanWikiText($trackInfo['wiki']['summary']);
            if ($this->get("wiki") !== $biography) {
                $this->set("wiki", $biography);
            }
        }

        return $this;
    }

    public function loadFromLastFm(array $trackInfo)
    {
        // Loading against null values will just populate the entity.
        return $this->compareData($trackInfo);
    }
}

<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Utility\Hash;

use App\Model\Api\LastfmApi;

class LastfmArtist extends Entity
{
    /**
     * Compare this version of the entity with the one passed in parameter.
     * The one in parameter is considered the new copy; 'this' is the one from the DB.
     */
    public function compareData(array $artistInfo)
    {
        $mbid = trim(Hash::get($artistInfo, "mbid"));
        if (!empty($mbid) && $this->get("mbid") !== $mbid) {
            $this->set("mbid", $mbid);
        }

        $url = trim(Hash::get($artistInfo, "url"));
        if (!empty($url) && $this->get("url") !== $url) {
            $this->set("url", $url);
        }

        $biography = Hash::check($artistInfo, 'bio') ? trim($artistInfo['bio']['content']) : '';
        if (!empty($biography)) {
            $biography = LastfmApi::cleanWikiText($artistInfo['bio']['content']);
            if ($this->get("biography") !== $biography) {
                $this->set("biography", $biography);
            }
        }

        return $this;
    }

    public function loadFromLastFm($artistInfo)
    {
        return $this->compareData($artistInfo);
    }
}

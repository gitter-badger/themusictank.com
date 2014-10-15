<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Api\LastfmApi;
use App\Model\Entity\SyncTrait;

class LastfmTrack extends Entity
{
    use SyncTrait;

    public function loadFromLastFm($trackInfo)
    {
        $this->mbid = $trackInfo["mbid"];
        $this->url = $trackInfo["url"];

        $this->wiki = __("Biography is not available at this time.");
        if (!empty($albumInfo['wiki']['summary'])) {
            $this->wiki = LastfmApi::cleanWikiText($albumInfo['wiki']['summary']);
        }
    }
}

<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Api\LastfmApi;
use App\Model\Entity\SyncTrait;

class LastfmAlbum extends Entity
{
    use SyncTrait;

    public function loadFromLastFm($albumInfo)
    {
        $this->mbid = $albumInfo["mbid"];
        $this->url = $albumInfo["url"];

        $this->wiki = __("Biography is not available at this time.");
        if (!empty($albumInfo['wiki']['summary'])) {
            $this->wiki = LastfmApi::cleanWikiText($albumInfo['wiki']['summary']);
        }
    }

}

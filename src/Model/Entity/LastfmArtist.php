<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
//use App\Model\Entity\ThumbnailTrait;
use App\Model\Api\LastfmApi;
//use App\Model\Entity\SyncTrait;

class LastfmArtist extends Entity
{
    //use ThumbnailTrait;//, SyncTrait;

    public function loadFromLastFm($artistInfo)
    {
        $this->mbid = $artistInfo["mbid"];
        $this->url = $artistInfo["url"];

        $this->biography = __("Biography is not available at this time.");
        if (!empty($artistInfo['bio']['content'])) {
            $this->biography = LastfmApi::cleanWikiText($artistInfo['bio']['content']);
        }
    }
}

<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Api\LastfmApi;

class LastfmArtist extends Entity
{
    public function loadFromLastFm($artistInfo)
    {
        $this->mbid = $artistInfo["mbid"];
        $this->url = $artistInfo["url"];

        if (!empty($artistInfo['bio']['content'])) {
            $this->biography = LastfmApi::cleanWikiText($artistInfo['bio']['content']);
        }
    }
}

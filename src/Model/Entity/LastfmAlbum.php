<?php
namespace App\Model\Entity;

use App\Model\Api\LastfmApi;
use App\Model\Entity\SyncTrait;

use Cake\Utility\Hash;
use Cake\ORM\Entity;

class LastfmAlbum extends Entity
{
    use SyncTrait;

    /**
     * Compare this version of the entity with the one passed in parameter.
     * The one in parameter is considered the new copy; 'this' is the one from the DB.
     */
    public function compareData(array $albumInfo)
    {
        $mbid = trim(Hash::get($albumInfo, "mbid"));
        if (!empty($mbid) && $this->get("mbid") !== $mbid) {
            $this->set("mbid", $mbid);
        }

        $url = trim(Hash::get($albumInfo, "url"));
        if (!empty($url) && $this->get("url") !== $url) {
            $this->set("url", $url);
        }

        $biography = Hash::check($albumInfo, 'wiki') ? trim($albumInfo['wiki']['summary']) : '';
        if (!empty($biography)) {
            $biography = LastfmApi::cleanWikiText($albumInfo['wiki']['summary']);
            if ($this->get("wiki") !== $biography) {
                $this->set("wiki", $biography);
            }
        }

        return $this;
    }

    public function loadFromLastFm(array $albumInfo)
    {
        // Loading against null values will just populate the entity.
        return $this->compareData($albumInfo);
    }
}

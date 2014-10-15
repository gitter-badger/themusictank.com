<?php
namespace App\Model\Entity;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;

trait SyncTrait {

    function requiresUpdate()
    {
        if (is_null($this->modified)) {
            return true;
        }

        $time = new Time($this->modified);
        return $time->toUnixString() < $this->getExpiredRange();
    }

    public function getExpiredRange()
    {
        return time() - (HOUR * 12);
    }

    public function hasSyncDate()
    {
        return !is_null($this->modified);
    }

    public function getFormattedSyncDate($dateFormat = "F j, g:i a")
    {
        $time = new Time($this->modified);
        return $time->timeAgoInWords();
     //   return $time->format($dateFormat);
    }
}

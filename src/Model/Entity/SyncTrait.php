<?php
namespace App\Model\Entity;

trait SyncTrait {

    public function getExpiredRange()
    {
        return time() - (HOUR * 12);
    }

    public function isExpired()
    {
        if($this->hasSyncDate()) {
            return $this->lastsync < $this->getExpiredRange();
        }
        return true;
    }

    public function hasSyncDate()
    {
        return (int)$this->lastsync > 0;
    }

    public function getFormattedSyncDate($dateFormat = "F j, g:i a")
    {
        return date($dateFormat, (int)$this->lastsync);
    }

}

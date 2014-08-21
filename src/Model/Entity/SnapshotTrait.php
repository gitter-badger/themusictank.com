<?php
namespace App\Model\Entity;

trait SnapshotTrait
{
    public function isNotAvailable()
    {
        return (int)$this->total === 1 && (int)$this->neutral === 1;
    }

    public function hasScore()
    {
        return !is_null($this->score) && $this->total > 1;
    }

}

<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\I18n\Time;

class Task extends Entity
{
    public function requiresUpdate()
    {
        if (is_null($this->modified)) {
            return true;
        }

        $time = new Time($this->modified);

        switch ($this->name) {
            case 'last_trackchallenge':
            case 'popular_artists':
                return $time->toUnixString() < (time() - 60*60*23.5);
        }

        return false;
    }
}

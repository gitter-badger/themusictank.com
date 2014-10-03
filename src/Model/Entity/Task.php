<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\I18n\Time;

class Task extends Entity
{
    public function requiresUpdate()
    {
        $time = new Time($this->modified);

        switch ($this->name) {
            case 'last_trackchallenge':
                return $time->toUnixString() < (time() - 60*60*23.5); // leave half an hour of breathing room for the cron.
        }

        return false;
    }
}

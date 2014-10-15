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
        $timeout = $this->getTimeout();

        return $time->toUnixString() < $time->toUnixString();
    }

    public function getTimeout() {
        switch ($this->name) {
            case 'artist_details':
                return new Time(time() - 60*60*24*14); // every two weeks

            // should be something like 'every tuesdays'
            case 'artists_discographies' :
                return new Time(time() - 60*60*24*2); // every two days

            default :
                return new Time(time() - 60*60*23.5); // shy of everyday
        }
    }

}

<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Entity\ThumbnailTrait;
use App\Model\Entity\SyncTrait;

class LastfmArtist extends Entity
{
    use ThumbnailTrait, SyncTrait;
}

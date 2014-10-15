<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Event\Event;
use Cake\ORM\Entity;

class LastfmArtistsTable extends Table
{
    public function initialize(array $config)
    {
        $this->belongsTo('Artists');
        $this->addBehavior('Syncable');
    }

    public function demotePopularArtists()
    {
        $this->updateAll(['is_popular' => true], ['is_popular' => false]);
    }

}

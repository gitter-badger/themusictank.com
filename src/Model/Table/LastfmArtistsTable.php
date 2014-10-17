<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\Utility\Hash;

class LastfmArtistsTable extends Table
{
    public function initialize(array $config)
    {
        $this->belongsTo('Artists');
        $this->addBehavior('Syncable');
    }

    public function demotePopularArtists()
    {
        $this->updateAll(['is_popular' => false], ['is_popular' => true]);
    }

    public function promotePopularArtists(array $artists)
    {
        $ids = Hash::map($artists, "{n}", function(Entity $artist) {
            return $artist->lastfm->id;
        });

        $this->updateAll(['is_popular' => true], ['id IN' => $ids]);
    }

}

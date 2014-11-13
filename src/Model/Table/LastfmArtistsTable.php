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

    public function findExpired(Query $query, array $options = [])
    {
        $options += [
            'timeout' => 0,
            'limit' => 200,
            'contain' => ['LastfmArtists']
        ];

        return $query
            ->contain($options['contain'])
            ->where(['LastfmArtists.modified < ' => (int)$options['timeout']])
            ->orWhere(['LastfmArtists.modified IS NULL'])
            ->limit((int)$options['limit']);
    }

}

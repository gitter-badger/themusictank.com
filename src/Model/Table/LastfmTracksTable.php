<?php
namespace App\Model\Table;

use Cake\I18n\Time;
use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

class LastfmTracksTable extends Table
{
    public function initialize(array $config)
    {
        $this->belongsTo('Tracks');
        $this->addBehavior('Syncable');
    }

    public function findExpired(Query $query, array $options = [])
    {
        $options += [
            'timeout' => new Time(),
            'limit' => 400,
            'contain' => ['Albums' => ['Artists'], 'LastfmTracks']
        ];

        return $query
            ->where(['LastfmAlbums.modified < ' => $options['timeout']->toUnixString()])
            ->orWhere(['LastfmAlbums.modified IS NULL'])
            ->contain($options['contain'])
            ->limit((int)$options['limit']);
    }
}

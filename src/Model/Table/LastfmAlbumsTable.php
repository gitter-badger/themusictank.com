<?php
namespace App\Model\Table;

use Cake\ORM\Table;

use App\Model\Entity\Artist;
use App\Model\Entity\Album;
use App\Model\Entity\LastfmAlbum;
use App\Model\Api\LastfmApi;

use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

class LastfmAlbumsTable extends Table
{
    public function initialize(array $config)
    {
        $this->belongsTo('Albums');
        $this->hasMany('Tracks');
        $this->addBehavior('Syncable');
    }

    public function findListMbids(Query $query, array $options)
    {
        if (get_class($options['artist']) !== "App\Model\Entity\Artist") {
            throw new Error('Missing artist entity.');
        }

        return $query->select(["mbid"])
            ->where(["Albums.artist_id" => $options['artist']->id])
            ->contain(['Albums'])
            ->extract("mbid")->toArray();
    }

    public function findExpired(Query $query, array $options = [])
    {
        $options += [
            'timeout' => 0,
            'limit' => 200,
            'contain' => ['Albums' => ['Artists', 'Tracks']]
        ];

        return $query
            ->where(['LastfmAlbums.modified < ' => $options['timeout']])
            ->orWhere(['LastfmAlbums.modified IS NULL'])
            ->contain($options['contain'])
            ->limit((int)$options['limit']);
    }

}

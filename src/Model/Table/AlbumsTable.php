<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class AlbumsTable extends Table {

    public function initialize(array $config)
    {
        $this->belongsTo('Artists');

        $this->hasMany('Tracks');

        $this->hasOne('LastfmAlbums', ['propertyName' => 'lastfm']);
        $this->hasOne('AlbumReviewSnapshots', ['propertyName' => 'snapshot']);
    }

    public function getBySlug($slug)
    {
        return $this->find()
            ->where(['Albums.slug' => $slug])
            ->contain(['Artists', 'LastfmAlbums', 'AlbumReviewSnapshots', 'Tracks']);
    }

    public function getNewReleases($limit = 1)
    {
        return $this->find()
            ->limit((int)$limit)
            ->order('release_date', 'DESC')
            ->contain(['Artists', 'LastfmAlbums', 'AlbumReviewSnapshots', 'Tracks']);
    }

    public function getByArtistId($artistId)
    {
        return $this->find()
            ->where(["artist_id" => $artistId])
            ->contain(['AlbumReviewSnapshots']);
    }

    public function getIdsByArtist($artistId)
    {
        return $this->find()
            ->select('id')
            ->where(["artist_id" => $artistId]);
    }

    public function searchCriteria($criteria, $limit = 10)
    {
        return $this->find()
            ->where(["Albums.name LIKE" => sprintf("%%%s%%", $criteria)])
            ->limit($limit)
            ->order("LOCATE('".$criteria."', Albums.name)", "ASC")
            ->order("Albums.name", "ASC")
            ->contain(['AlbumReviewSnapshots', 'Artists']);
    }

}

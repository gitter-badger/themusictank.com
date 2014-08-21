<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class TracksTable extends Table {

    public function initialize(array $config)
    {
        $this->belongsTo('Albums');

        $this->hasOne('LastfmTracks', ['propertyName' => 'lastfm']);
        $this->hasOne('TrackReviewSnapshots', ['propertyName' => 'snapshot']);
        $this->hasOne('TrackYoutubes', ['propertyName' => 'youtube']);
    }

    public function getBySlug($slug)
    {
        return $this->find()
            ->where(['Tracks.slug' => $slug])
            ->contain(['Albums' => ['Artists'], 'LastfmTracks', 'TrackReviewSnapshots', 'TrackYoutubes']);
    }

    public function getByAlbumIds($albumIds)
    {
        return $this->find()
            ->where(["album_id IN" => $albumIds])
            ->contain(['TrackReviewSnapshots']);
    }

    public function getByAlbumId($albumId)
    {
        return $this->find()
            ->where(["album_id" => $albumId])
            ->contain(['TrackReviewSnapshots']);
    }

    public function searchCriteria($criteria, $limit = 10)
    {
        return $this->find()
            ->where(["title LIKE" => sprintf("%%%s%%", $criteria)])
            ->limit($limit)
            ->order("LOCATE('".$criteria."', title)", "ASC")
            ->order("title", "ASC")
            ->contain(['LastfmTracks', 'Albums', 'TrackReviewSnapshots']);
    }

}

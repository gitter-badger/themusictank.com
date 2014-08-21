<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class UserAlbumReviewSnapshotsTable extends Table {

    public function initialize(array $config) {
        $this->belongsTo('Users');
        $this->belongsTo('Albums');
    }

    public function getByAlbumId($albumId)
    {
        return $this->find()
            ->where(["album_id" => $albumId])
            ->contain(['Users', 'Albums']);
    }

}

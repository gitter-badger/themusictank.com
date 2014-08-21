<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class SubscriptionAlbumReviewSnapshotsTable extends Table {

    public function initialize(array $config) {
        $this->belongsTo('Users');
        $this->belongsTo('Albums');
    }

    public function getByAlbumIdAndUserId($albumId, $userId)
    {
        return $this->find()
            ->where([
                "album_id" => $albumId,
                "user_id" => $userId
            ])
            ->contain(['Users', 'Albums']);
    }

}

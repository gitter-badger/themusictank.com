<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class UserTrackReviewSnapshotsTable extends Table {

    public function initialize(array $config) {
        $this->belongsTo('Users');
        $this->belongsTo('Tracks');
    }

    public function getByTrackId($trackId)
    {
        return $this->find()
            ->where(["track_id" => $trackId])
            ->contain(['Users', 'Tracks']);
    }

}

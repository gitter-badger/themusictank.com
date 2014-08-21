<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class TrackReviewSnapshotsTable extends Table {

    public function initialize(array $config) {
        $this->belongsTo('Tracks');
    }

}

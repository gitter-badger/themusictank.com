<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class AlbumReviewSnapshotsTable extends Table {

    public function initialize(array $config) {
        $this->belongsTo('Albums');
    }

}

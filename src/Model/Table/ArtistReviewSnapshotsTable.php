<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class ArtistReviewSnapshotsTable extends Table {

    public function initialize(array $config) {
        $this->belongsTo('Artists');
    }

}

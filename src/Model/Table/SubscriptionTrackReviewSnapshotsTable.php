<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class SubscriptionsTrackReviewSnapshotsTable extends Table {

    public function initialize(array $config) {
        $this->belongsTo('Users');
        $this->belongsTo('Tracks');
    }

}

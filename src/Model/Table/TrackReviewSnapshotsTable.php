<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class TrackReviewSnapshotsTable extends SnapshotsTable {

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Tracks');
    }

    public function getBelongsToPrefix()
    {
        return 'track';
    }

}

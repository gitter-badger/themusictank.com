<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class ArtistReviewSnapshotsTable extends SnapshotsTable {

    public function initialize(array $config)
    {
        $this->belongsTo('Artists');
    }

    public function getBelongsToPrefix()
    {
        return 'artist';
    }

}

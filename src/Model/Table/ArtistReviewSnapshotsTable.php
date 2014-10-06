<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class ArtistReviewSnapshotsTable extends SnapshotsTable {

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Artists');
    }

    public function getBelongsToPrefix()
    {
        return 'artist';
    }

}

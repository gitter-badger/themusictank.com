<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class AlbumReviewSnapshotsTable extends SnapshotsTable {

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Albums');
    }

    public function getBelongsToPrefix()
    {
        return 'album';
    }
}

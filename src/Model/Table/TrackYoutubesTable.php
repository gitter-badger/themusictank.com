<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class TrackYoutubesTable extends Table {

    public function initialize(array $config) {
        $this->belongsTo('Tracks');
    }

}

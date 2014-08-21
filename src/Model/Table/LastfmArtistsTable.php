<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class LastfmArtistsTable extends Table
{

    public function initialize(array $config)
    {
        $this->belongsTo('Artists');
    }


}

<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class LastfmArtistsTable extends Table
{

    public function initialize(array $config)
    {
        $this->belongsTo('Artists');
    }

    public function demotePopularArtists()
    {
        $this->updateAll(['is_popular' => true], ['is_popular' => false]);
    }

}

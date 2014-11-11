<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class LastfmTracksTable extends Table
{

    public function initialize(array $config)
    {
        $this->belongsTo('Tracks');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always'
                ]
            ]
        ]);
    }

}
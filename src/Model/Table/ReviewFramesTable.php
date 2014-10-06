<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class ReviewFramesTable extends Table {

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new'
                ]
            ]
        ]);

       /* $this->hasOne('Artists');
        $this->hasOne('Albums');
        $this->hasOne('Tracks');
        $this->hasOne('Users');*/
    }


    public function getTotal()
    {
        $query = $this->find();
        return $query
            ->group('position')
            ->select([
                'quantity' => $query->func()->count('id')
            ]);
    }

    public function getLikes()
    {
        $query = $this->find();
        return $query
            ->group('position')
            ->select([
                'quantity' => $query->func()->count('id'),
                'avg_groove' => $query->func()->avg('groove')
            ])
            ->having(['avg_groove' > 0.75]);
    }

    public function getDislikes()
    {
        $query = $this->find();
        return $query
            ->group('position')
            ->select([
                'quantity' => $query->func()->count('id'),
                'avg_groove' => $query->func()->avg('groove')
            ])
            ->having([
                'avg_groove' < 0.25,
                'avg_groove' > 0
            ]);
    }

    public function getAverageCurve()
    {
        $query = $this->find();
        return $query
            ->select([
                'avg_groove' => $query->func()->avg('groove'),
                'position'
            ])
            ->order(['position' => 'ASC'])
            ->group('position');
    }


    public function getCurveScan($scanWidth = 15)
    {
        $query = $this->find();
        return $query
            ->select([
                'avg_groove' => $query->func()->avg('groove'),
                'area' => sprintf('ROUND(position / %d)', $scanWidth)
            ])
            ->group('area')
            ->order(['avg_groove' => 'ASC'])
            ->limit(1);
    }

}

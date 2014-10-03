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

    public function getAppreciation($condition)
    {
        $query = $this->find();


        $likingQuery = $query->select([
            'total_qty' => $query->func()->count('id'),
            'avg_groove' => $query->func()->avg('groove')
        ])
        ->group('position')
        ->all();

        $likingQuery = $query->select([
            'total_qty' => $query->func()->count('id'),
            'avg_groove' => $query->func()->avg('groove')
        ])
        ->group('position')
        ->having(['avg_groove >' => .75])
        ->all();

        $dislikingQuery = $query->select([
            'total_qty' => $query->func()->count('id'),
            'avg_groove' => $query->func()->avg('groove')
        ])
        ->group('position')
        ->having(['avg_groove <' => .25, 'avg_groove >' => 0])
        ->all();


/*

        //->having(['count >' => 3]);





        $dataT1 = $this->query("SELECT SUM(total_qty) as qty FROM (SELECT count(*) AS total_qty, AVG(groove) AS avg_groove FROM review_frames AS ReviewFrames WHERE $condition group by position) as t1;");
        $dataT2 = $this->query("SELECT SUM(liking_qty) as qty FROM (SELECT count(*) AS liking_qty, AVG(groove) AS avg_groove FROM review_frames AS ReviewFrames WHERE $condition group by position HAVING avg_groove > .75) as t2");
        $dataT3 = $this->query("SELECT SUM(disliking_qty) as qty FROM (SELECT count(*) AS disliking_qty, AVG(groove) AS avg_groove FROM review_frames AS ReviewFrames WHERE $condition group by position HAVING avg_groove < .25 && avg_groove > 0) as t3");

        $liking     = (int)Hash::get($dataT2, "0.0.qty");
        $disliking  = (int)Hash::get($dataT3, "0.0.qty");
        $total      = (int)Hash::get($dataT1, "0.0.qty");
        $neutral    = $total - $disliking - $liking;

        // this prevents divisions by 0
        if($total < 1)
        {
            $total = 1;
            $neutral = 1;
        }

        return array(
            "liking"        => $liking,
            "disliking"     => $disliking,
            "neutral"       => $neutral,
            "liking_pct"    => $liking      / $total * 100,
            "disliking_pct" => $disliking   / $total * 100,
            "neutral_pct"   => $neutral     / $total * 100,
            "total"         => $total
        );*/
    }

}

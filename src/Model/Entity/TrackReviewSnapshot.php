<?php
namespace App\Model\Entity;

use App\Model\Entity\ReviewSnapshot;

class TrackReviewSnapshot extends ReviewSnapshot
{
    public function customizeQuery($query)
    {
        return $query->where(['track_id' => $this->track_id]);
    }
}

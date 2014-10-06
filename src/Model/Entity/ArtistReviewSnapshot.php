<?php
namespace App\Model\Entity;

use App\Model\Entity\ReviewSnapshot;

class ArtistReviewSnapshot extends ReviewSnapshot
{
    public function customizeQuery($query)
    {
        return $query->where(['artist_id' => $this->artist_id]);
    }
}

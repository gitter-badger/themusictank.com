<?php
namespace App\Model\Entity;

use App\Model\Entity\ReviewSnapshot;

class AlbumReviewSnapshot extends ReviewSnapshot
{
    public function customizeQuery($query)
    {
        return $query->where(['album_id' => $this->album_id]);
    }
}

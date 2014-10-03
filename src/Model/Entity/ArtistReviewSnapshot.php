<?php
namespace App\Model\Entity;

use App\Model\Entity\ReviewSnapshot;

class ArtistReviewSnapshot extends ReviewSnapshot
{

    public function fetch()
    {
        return parent::updateCache(["ReviewFrames.artist_id" => $this->artist_id]);
    }
}

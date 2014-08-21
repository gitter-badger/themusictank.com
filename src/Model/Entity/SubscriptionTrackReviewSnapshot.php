<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Entity\SnapshotTrait;

class SubscriptionTrackReviewSnapshot extends Entity
{
    use SnapshotTrait;

    public function getByTrackIdAndUserId($trackId, $userId)
    {
        return $this->find()->where([
            'track_id' => $trackId,
            'user_id' => $userId,
        ]);
    }
}

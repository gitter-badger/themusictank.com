<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Entity\SnapshotTrait;

class SubscriptionAlbumReviewSnapshot extends Entity
{
    use SnapshotTrait;

    public function getByAlbumIdAndUserId($albumId, $userId)
    {
        return $this->find()->where([
            'album_id' => $albumId,
            'user_id' => $userId,
        ]);
    }
}

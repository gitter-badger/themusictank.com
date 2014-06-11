<?php
App::uses('TableSnapshot', 'Model');
App::uses('CakeSession', 'Model/Datasource');

class SubscribersAlbumReviewSnapshot extends TableSnapshot
{
	public $name        = 'SubscribersAlbumReviewSnapshot';
	public $useTable    = 'subscribers_album_review_snapshots';
	public $belongsTo   = array('Album', 'User');

	public function fetch($albumId, $userIds)
	{
		if(count($userIds)) {
			$existing = $this->findByAlbumIdAndUserId($albumId, $userIds);
			if($existing) {
				$this->data[$this->alias] = Hash::extract($existing, $this->alias);
			}

	        $result = $this->updateCached(array("ReviewFrames.album_id" => $albumId, "ReviewFrames.user_id" => $userIds));
	        if($result) return $result;
		}

        return array();
	}

    public function getExtraSaveFields($conditions = array())
    {
    	$extra = parent::getExtraSaveFields($conditions);
		$extra = Hash::insert($extra, "album_id", $conditions["ReviewFrames.album_id"]);
		$extra = Hash::insert($extra, "user_id", $conditions["ReviewFrames.user_id"]);
    	return $extra;
    }
}

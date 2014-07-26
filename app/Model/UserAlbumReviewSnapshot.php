<?php
App::uses('TableSnapshot', 'Model');
App::uses('CakeSession', 'Model/Datasource');

class UserAlbumReviewSnapshot extends TableSnapshot
{
	public $name        = 'UserAlbumReviewSnapshot';
    public $useTable    = 'user_album_review_snapshots';
    public $belongsTo   = array('Album', 'User');

    public function getAppreciation($belongsToId, $timestamp = 0, $extraConditions = null)
    {
        return parent::getappreciation($belongsToId, $timestamp, "user_id = " . CakeSession::read('Auth.User.User.id'));
    }

    public function fetch($albumId, $userId)
    {
		$existing = $this->findByAlbumIdAndUserId($albumId, $userId);
		if($existing) {
			$this->data[$this->alias] = Hash::extract($existing, $this->alias);
		}

        return $this->updateCached(array("ReviewFrames.album_id" => $albumId, "ReviewFrames.user_id" => $userId));
    }

    public function getExtraSaveFields($conditions = array())
    {
    	$extra = parent::getExtraSaveFields($conditions);
    	$extra = Hash::insert($extra, "album_id", $conditions["ReviewFrames.album_id"]);
    	$extra = Hash::insert($extra, "user_id", $conditions["ReviewFrames.user_id"]);
    	return $extra;
    }

}

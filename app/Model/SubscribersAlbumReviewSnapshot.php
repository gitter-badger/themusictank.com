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
		return $this->updateCached( array("ReviewFrames.album_id"=>$albumId, "user_id" => $userIds) );
	}
}

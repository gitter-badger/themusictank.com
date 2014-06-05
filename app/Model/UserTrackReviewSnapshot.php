<?php
App::uses('TableSnapshot', 'Model');
App::uses('CakeSession', 'Model/Datasource');

class UserTrackReviewSnapshot extends TableSnapshot
{
	public $name        = 'UserTrackReviewSnapshot';
    public $useTable    = 'user_track_review_snapshots';
    public $belongsTo   = array('Track', 'User');

    public function fetch($trackId, $userIds) {

    	$conditions = array();
    	$conditions = Hash::insert($conditions, "track_id", $trackId);
    	$conditions = Hash::insert($conditions, "user_id", $userIds);

    	return $this->updateCached( $conditions );
    }
}

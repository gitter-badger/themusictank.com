<?php
App::uses('TableSnapshot', 'Model');
class TrackReviewSnapshot extends TableSnapshot
{
	public $name        = 'TrackReviewSnapshot';
    public $useTable    = 'track_review_snapshots';
    public $belongsTo   = array('Track');

    public function fetch($trackId) {
    	return $this->updateCached( Hash::insert(array(), "track_id", $trackId) );
    }

}

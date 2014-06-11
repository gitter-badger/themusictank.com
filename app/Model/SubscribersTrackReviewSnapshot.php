<?php
App::uses('TableSnapshot', 'Model');
App::uses('CakeSession', 'Model/Datasource');

class SubscribersTrackReviewSnapshot extends TableSnapshot
{
	public $name        = 'SubscribersTrackReviewSnapshot';
    public $useTable    = 'subscribers_track_review_snapshots';
    public $belongsTo   = array('Track', 'User');

    public function fetch($trackId, $userIds)
    {
		$existing = $this->findByTrackIdAndUserId($trackId, $userIds);
		if($existing) {
			$this->data[$this->alias] = Hash::extract($existing, $this->alias);
		}

        $result = $this->updateCached(array("ReviewFrames.track_id" => $trackId, "ReviewFrames.user_id" => $userIds));
        return ($result)  ? $result : array();
    }

    public function getExtraSaveFields($conditions = array())
    {
    	$extra = parent::getExtraSaveFields($conditions);
		$extra = Hash::insert($extra, "track_id", $conditions["ReviewFrames.track_id"]);
		$extra = Hash::insert($extra, "user_id", $conditions["ReviewFrames.user_id"]);
    	return $extra;
    }

}

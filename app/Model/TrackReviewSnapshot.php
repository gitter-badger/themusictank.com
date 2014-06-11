<?php
App::uses('TableSnapshot', 'Model');
class TrackReviewSnapshot extends TableSnapshot
{
	public $name        = 'TrackReviewSnapshot';
    public $useTable    = 'track_review_snapshots';
    public $belongsTo   = array('Track');

    public function fetch($trackId)
    {
		$existing = $this->findByTrackId($trackId);
		if ($existing) {
			$this->data[$this->alias] = Hash::extract($existing, $this->alias);
		}

        $result = $this->updateCached(array("ReviewFrames.track_id" => $trackId));
        return ($result)  ? $result : array();
    }

    public function getExtraSaveFields($conditions = array())
    {
    	$extra = parent::getExtraSaveFields($conditions);
	   	$extra = Hash::insert($extra, "track_id", $conditions["ReviewFrames.track_id"]);
    	return $extra;
    }
}

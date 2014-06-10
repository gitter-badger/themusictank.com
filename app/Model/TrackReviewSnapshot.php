<?php
App::uses('TableSnapshot', 'Model');
class TrackReviewSnapshot extends TableSnapshot
{
	public $name        = 'TrackReviewSnapshot';
    public $useTable    = 'track_review_snapshots';
    public $belongsTo   = array('Track');

    public function fetch($trackId)
    {
		$this->data["Track"] = array("id" => $trackId);

		$existing = $this->findByTrackId($trackId);
		if ($existing) {
			$this->data["TrackReviewSnapshot"] = Hash::extract($existing, "TrackReviewSnapshot");
		}

        $result = $this->updateCached(array("ReviewFrames.track_id" => $trackId));

        return ($result)  ? $result : array();
    }
}

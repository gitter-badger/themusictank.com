<?php
App::uses('TableSnapshot', 'Model');
class ArtistReviewSnapshot extends TableSnapshot
{
	public $name        = 'ArtistReviewSnapshot';
    public $useTable    = 'artist_review_snapshots';
    public $belongsTo   = array('Artist');

    public function fetch($artistId)
    {
		$existing = $this->findByArtistId($artistId);
		if ($existing) {
			$this->data[$this->alias] = Hash::extract($existing, $this->alias);
		}

        $result = $this->updateCached(array("ReviewFrames.artist_id" => $artistId));
        return ($result)  ? $result : array();
    }

    public function getExtraSaveFields($conditions = array())
    {
    	$extra = parent::getExtraSaveFields($conditions);
		$extra = Hash::insert($extra, "artist_id", $conditions["ReviewFrames.artist_id"]);
    	return $extra;
    }
}

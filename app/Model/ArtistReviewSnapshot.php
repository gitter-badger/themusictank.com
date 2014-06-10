<?php
App::uses('TableSnapshot', 'Model');
class ArtistReviewSnapshot extends TableSnapshot
{
	public $name        = 'ArtistReviewSnapshot';
    public $useTable    = 'artist_review_snapshots';
    public $belongsTo   = array('Artist');

    public function fetch($artistId)
    {
		$this->data["Artist"] = array("id" => $artistId);

		$existing = $this->findByArtistId($artistId);
		if ($existing) {
			$this->data["ArtistReviewSnapshot"] = Hash::extract($existing, "ArtistReviewSnapshot");
		}

        $result = $this->updateCached( array("ReviewFrames.artist_id" => $artistId) );
        return ($result)  ? $result : array();
    }
}

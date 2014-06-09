<?php
App::uses('TableSnapshot', 'Model');
class ArtistReviewSnapshot extends TableSnapshot
{
	public $name        = 'ArtistReviewSnapshot';
    public $useTable    = 'artist_review_snapshots';
    public $belongsTo   = array('Artist');

    public function fetch($artistId)
    {
        return $this->updateCached( array("ReviewFrames.artist_id" => $artistId) );
    }

}

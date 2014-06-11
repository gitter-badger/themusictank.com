<?php
App::uses('UserReviewSnapshot', 'Model');
App::uses('CakeSession', 'Model/Datasource');

class UserArtistReviewSnapshot extends UserReviewSnapshot
{
	public $name        = 'UserArtistReviewSnapshot';
    public $useTable    = 'user_artist_review_snapshots';
    public $belongsTo   = array('Artist', 'User');


}

<?php

App::uses('ArtistReviewSnapshot', 'Model');

class Artist extends AppModel
{
	public $hasOne 	= array('LastfmArtist', 'ArtistReviewSnapshot');
    public $hasMany = array('Albums' => array('order' => array('Albums.notability ASC')));
    public $order 	= array('Artist.name ASC');
    public $actsAs   = array('ThumbnailLeech');
	public $validate = array(
		'name' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'A name is required'
			)
		)
	);

    public function beforeSave($options = array())
    {
        // Ensure the data has a valid unique slug
        $this->checkSlug(array('name'));
        return true;
    }

    public function search($query, $limit = 10)
    {
        // Get an updated result set from LastFm before
        // fetching our own results. This is to keep
        // our database up to date.
        $this->LastfmArtist->search($query, $limit);

        // Secondly, query our own database for results.
        return $this->find('all', array(
            "conditions" => array("Artist.name LIKE" => sprintf("%%%s%%", $query)),
            "fields"     => array("Artist.slug", "Artist.name", "LastfmArtist.image"),
            "limit"      => $limit,
            "order"		 => array("LOCATE('".$query."', Artist.name)", "Artist.name")
        ));
    }

    public function getBySlug($slug)
    {
    	$this->data = $this->find("first", array(
            "conditions" => array("Artist.slug" => $slug),
            "fields"    => array("Artist.*", "LastfmArtist.*")
        ));

        return $this->data;
    }

    public function getSnapshot()
    {
        $reviews = new ArtistReviewSnapshot();
        $reviews->data = array(
            "Artist" => array(
                "id" => $this->getData("Artist.id"),
                "name" => $this->getData("Artist.name")
            )
        );
        return $reviews->fetch($this->getData("Artist.id"));
    }

    public function getUserSnapshot($userId)
    {
        $reviews = new UserArtistReviewSnapshot();
        return $reviews->fetch($this->getData("Artist.id"), $userId);
    }

    public function getUserSubscribersSnapshot($userIds)
    {
        $reviews = new SubscribersArtistReviewSnapshot();
        return $reviews->fetch($this->getData("Artist.id"), $userIds);
    }

    /**
     * Finds all artists that have been flagged as popular.
     * @return array Dataset of popular Artists.
     */
    public function findAllPopular()
    {
        return $this->findPopular();
    }

    /**
     * Finds all artists that have been flagged as popular.
     * @return array Dataset of popular Artists.
     */
    public function findPopular($limit = null)
    {
        return $this->find("all", array("conditions" => array("LastfmArtist.is_popular" => true), "limit" => $limit, "order" => "rand()"));
    }

    /**
     * Fetches a list of possible categories based on the name of all our artists
     * @return array An array of capitalized letters
     */
    public function getCategories()
    {
        $data = $this->query("SELECT DISTINCT LEFT(name, 1) as letter FROM artists as Artist ORDER BY letter");
        return Hash::extract($data, "{n}.0.letter");
    }
}

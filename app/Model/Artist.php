<?php

App::uses('ArtistReviewSnapshot', 'Model');

class Artist extends AppModel
{	    
	public $hasOne = array('RdioArtist', /*'EchonestArtist',*/ 'LastfmArtist' /*, "ArtistReviewSnapshot"*/);	
    public $hasMany = array('Albums' => array('order' => array('Albums.notability DESC', 'Albums.release_date DESC')));
    public $order = "name ASC";    
	public $validate = array(
		'name' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'A name is required'
			)
		)
	);

    public function search($query, $limit = 10)
    {
        return $this->find('all', array(
            "conditions" => array("Artist.name LIKE" => sprintf("%%%s%%", $query)),
            "fields"     => array("Artist.slug", "Artist.name"),
            "limit"      => $limit
        ));
    }
    
    public function getUpdatedSetBySlug($slug, $addCurrentUser = false)
    {
        $syncValues = $this->find("first", array(
            "conditions" => array("Artist.slug" => $slug),
            "fields"    => array("RdioArtist.*", "Artist.*", "LastfmArtist.*")
        ));
        
        if(count($syncValues)) {
            $this->RdioArtist->data = $syncValues;        
            $this->RdioArtist->updateCached();
            $syncValues["RdioArtist"] = $this->RdioArtist->data["RdioArtist"];
                          
            $this->LastfmArtist->data = $syncValues;        
            $this->LastfmArtist->updateCached();
            $syncValues["LastfmArtist"] = $this->LastfmArtist->data["LastfmArtist"];
                    
            // Everything has been sync'd. Fetch every field we have.
            $this->data = $syncValues;
            return $syncValues; //$this->findBySlug($slug);
        }
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

    public function beforeSave($options = array())
    {
        // Ensure the data has a valid unique slug
        $this->checkSlug(array('name'));
        return true;
    }
    
    public function filterNewAndSave($artistList)
    {
        $list = $this->RdioArtist->filterNew($artistList);
        return $this->saveMany($list, array('deep' => true));                
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
        return $this->find("all", array("conditions" => array("RdioArtist.is_popular" => true), "limit" => $limit));
    }

    
    /**
     * Fetches a list of possible categories based on the name of all our artists
     * @return array An array of capitalized letters
     */
    public function getCategories()
    {
        $data = $this->query("SELECT DISTINCT LEFT(name, 1) as letter FROM artists as Artist ORDER BY letter");
        $letters = array();
        
        foreach($data as $row)
        {
            $letters[] = $row[0]["letter"];
        }
        
        return $letters;
    }
}
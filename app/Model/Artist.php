<?php

//App::uses('User', 'Model');

class Artist extends AppModel
{	    
	public $hasOne = array('RdioArtist', /*'EchonestArtist',*/ 'LastfmArtist', "ArtistReviewSnapshot");	
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

    public function search($query)
    {
        return $this->find('all', array(
            "conditions" => array("Artist.name LIKE" => sprintf("%%%s%%", $query)),
            "fields"     => array("Artist.slug", "Artist.name"),
            "limit"      => 10
        ));
    }
    
    public function getUpdatedSetBySlug($slug, $addCurrentUser = false)
    {
        $syncValues = $this->find("first", array(
            "conditions" => array("Artist.slug" => $slug),
            "fields"    => array("RdioArtist.*", "Artist.id", "Artist.name", "LastfmArtist.image", "LastfmArtist.id", "LastfmArtist.lastsync", "ArtistReviewSnapshot.*")
        ));
        
        $this->RdioArtist->data = $syncValues;        
        $this->RdioArtist->updateCached();
                      
        $this->LastfmArtist->data = $syncValues;        
        $this->LastfmArtist->updateCached();
                
        $this->ArtistReviewSnapshot->data = $syncValues;    
        $this->ArtistReviewSnapshot->updateCached();
                
        /* Feature is currently broken. We dont want graphs on the whole discography
         * we would rather have avg scores
        if($addCurrentUser)
        {   
            $user = new User();
            $user->UserArtistReviewSnapshot->data = $syncValues;
            $user->UserArtistReviewSnapshot->updateCached();            
        }*/
                
        // Everything has been sync'd. Fetch every field we have.
        return $this->findBySlug($slug);
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
        return $this->find("all", array("conditions" => array("RdioArtist.is_popular" => true)));
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
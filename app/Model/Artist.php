<?php

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
	    
    public function beforeSave($options = array())
    {
        // Ensure the data has a valid unique slug
        $this->checkSlug(array('name'));
        return true;
    }
    
    public function afterSave($created, $options = array())
    {
        
        // Validating against the auth component because the cron launches this.
        if($created && (int)AuthComponent::user('id') > 0) 
        {
            $this->dispatchEvent('onCreate');
        }
    }
    
    public function filterNewAndSave($artistList)
    {
        return $this->saveMany($this->filterNew($artistList), array('deep' => true));                
    }
    
    public function filterNew($artistList)
    {
        return $this->RdioArtist->filterNew($artistList);
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
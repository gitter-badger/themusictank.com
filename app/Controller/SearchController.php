<?php
class SearchController extends AppController {

    public $components = array('Paginator');
 	
	public function index()
	{
		if($this->request->is('get'))
        {
        	$this->loadModel("Track");        	
        	$this->loadModel("Album");
        	$this->loadModel("Artist");

			$query = trim($this->request->query['q']);

            $this->set('query', $query);
            $this->set('tracks', $this->Track->search($query));
            $this->set('albums', $this->Album->search($query));
            $this->set('artists', $this->Artist->search($query));
        }
	}


}
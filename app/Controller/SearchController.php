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
            $this->set('tracks', $this->Track->search($query, 5));
            $this->set('albums', $this->Album->search($query, 5));
            $this->set('artists', $this->Artist->search($query, 5));
            // fetch 5 results but show 4. That way we know when there are more to be found.
            $this->set('maxResults', 4);
        }
	}
}

<?php
namespace App\Controller;

use App\Controller\AppController;

class SearchController extends AppController {

    public function index()
    {
        if($this->request->is('get'))
        {
            $this->loadModel("Track");
            $this->loadModel("Album");
            $this->loadModel("Artist");

            $queryStr = trim($this->request->query['q']);
            //$artistResults = $this->Artist->searchCriteria($queryStr, 5)->all();
            //$albumResults = $this->Album->searchCriteria($queryStr, 5)->all();
            //$trackResults = $this->Track->searchCriteria($queryStr, 5)->all();

            $this->set([
                'query'     => $queryStr,
                'tracks'    => null,//$trackResults,
                'albums'    => null,//$albumResults,
                'artists'   => null,//$artistResults,
                // fetch 5 results but show 4. That way we know when there are more to be found.
                'maxResults' => 4
            ]);

        }
    }
}

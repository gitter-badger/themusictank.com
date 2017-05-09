<?php

namespace App\Services;

use App\Models\Track;
use Google_Client;
use Google_Service_YouTube;

class YoutubeService
{
    private $yt;
    private $client;

    public function __construct($key)
    {
        $this->client = new Google_Client();
        $this->client->setDeveloperKey($key);
        $this->yt = new Google_Service_YouTube($client);
    }

    public function getKey(Track $track)
    {
        $results = $this->getSearchClient()->listSearch('*', [
            'q' => $this->makeQuery($track),
            'maxResults' => 1,
        ]);

        return $results['items'][0]['id']['videoId'];
    }

    private function getSearchClient()
    {
        return $yt->search;
    }

    private function makeQuery(Track $track)
    {
        return $track->name . " by " . $track->album->artist->name . " from " . $track->album->name;
    }

}

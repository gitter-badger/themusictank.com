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
        $this->yt = new Google_Service_YouTube($this->client);
    }

    public function getKey(Track $track)
    {
        $results = $this->yt->search->listSearch('id,snippet', [
            'q' => $this->makeQuery($track),
            'maxResults' => 1,
        ]);

        return $results['items'][0]['id']['videoId'];
    }

    private function makeQuery(Track $track)
    {
        return sprintf(
            "%s by %s from %s",
            $track->name,
            $track->artist->name,
            $track->album->name
        );
    }

}

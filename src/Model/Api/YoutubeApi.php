<?php

namespace App\Model\Api;

use Cake\Utility\Hash;
use Cake\Core\Configure;
use App\Model\Entity\Track;
use Cake\Network\Http\Client;

class YoutubeApi {

    const YOUTUBE_QUERY_URL = "http://gdata.youtube.com/feeds/api/videos";

    private function _postRequest($params = [])
    {
        $params["alt"] = "json";
        $params["max-results"] = 1;

        $http = new Client();
        $results = $http->get(self::YOUTUBE_QUERY_URL, $params);

        if ($results->isOk())
        {
            return json_decode($results->body(), true);
        }

        return null;
    }

    public function getVideoId(Track $track)
    {
        $data = $this->_postRequest(["q" =>  $track->album->artist->name . "-" . $track->title]);
        $linksToVideos = Hash::extract($data, "feed.entry.{n}.link.{n}.href");

        foreach ($linksToVideos as $href) {
            preg_match("/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/", $href, $matches);
            if (count($matches) > 1 && strlen($matches[2]) === 11) {
                return $matches[2];
            }
        }

        return null;
    }
}

<?php
App::uses('HttpSocket', 'Network/Http');
class YoutubeBehavior extends ModelBehavior {

    const YOUTUBE_QUERY_URL = "http://gdata.youtube.com/feeds/api/videos";

    public function getRemoteVideoId($model, $query)
    {
        $HttpSocket = new HttpSocket();
        $response = $HttpSocket->get(self::YOUTUBE_QUERY_URL, array(
            "alt" => "json",
            "max-results" => 1,
            "q" => $query
        ));

        if($response->isOk())
        {
            $result = json_decode($response->body());

            foreach($result->feed->entry[0]->link as $link)
            {
                preg_match("/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/", $link->href, $matches);
                if (count($matches) > 1 && strlen($matches[2]) === 11) {
                    return $matches[2];
                }
            }
        }
    }
}

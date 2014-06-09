<?php
App::uses('HttpSocket', 'Network/Http');
class LastfmBehavior extends ModelBehavior {

    private function _post($params)
    {
        $config = Configure::read("LastFmApiConfig");
        $params["api_key"] = $config["key"];
        $params["format"] = "json";

        $HttpSocket = new HttpSocket();
        $results = $HttpSocket->get('http://ws.audioscrobbler.com/2.0/', http_build_query($params));

        if ($results->isOk())
        {
            return json_decode($results->body());
        }

        return false;
    }

    public function getLastFmTrackDetails($model, $trackName, $artistName)
    {
        $data = $this->_post(array("method" => "track.getinfo", "artist" => $artistName, "track" => $trackName));
        return (!is_null($data) && $data->track) ? $data->track : null;
    }

    public function getLastFmAlbumDetails($model, $artistName, $albumName)
    {
        $data = $this->_post(array("method" => "album.getinfo", "artist" => $artistName, "album" => $albumName));
        return (!is_null($data) && $data->album) ? $data->album : null;
    }

    public function cleanLastFmWikiText($model, $text)
    {
        $text = trim($text);
        $text = preg_replace('/Read more about .* on .*/', '', $text);
        $text = preg_replace('/User-contributed text is available under the Creative Commons By-SA License and may also be available under the GNU FDL./', '', $text);
        $text = strip_tags($text);
        $text = str_replace(array("\r\n", "\r"), "\n", $text);
        $lines = explode("\n", $text);
        $new_lines = array();

        foreach ($lines as $i => $line) {
            $data = trim($line);
            if(!empty($data)) $new_lines[] = $data;
        }

        return "<p>" . implode("</p>\n<p>", $new_lines) . "</p>";
    }

    public function getArtistInfo($model, $artistName)
    {
        $data = $this->_post(array("method" => "artist.getinfo", "artist" => $artistName));
        return (!is_null($data) && $data->artist) ? $data->artist : null;
    }

    public function getArtistTopAlbums($model, $artistName)
    {
        $data = $this->_post(array("method" => "artist.gettopalbums", "artist" => $artistName));
        return ($data->topalbums) ? $data->topalbums->album : null;
    }

    public function searchArtists($model, $query, $limit)
    {
        $data = $this->_post(array("method" => "artist.search", "artist" => $query, "limit" => $limit));
        return (!is_null($data) && is_object($data->results->artistmatches)) ? $data->results->artistmatches : null;
    }

    public function searchAlbums($model, $query, $limit)
    {
        $data = $this->_post(array("method" => "album.search", "album" => $query, "limit" => $limit));
        return (is_object($data->results->albummatches)) ? $data->results->albummatches : null;
    }

    public function searchTracks($model, $query, $limit)
    {
        $data = $this->_post(array("method" => "track.search", "track" => $query, "limit" => $limit));
        return (is_object($data->results->trackmatches)) ? $data->results->trackmatches : null;
    }

    public function getTopArtists($model)
    {
    	$query = array("country" => "United States");
        $data = $this->_post(array("method" => "geo.getTopArtists", $query));
        return (!is_null($data) && is_object($data->topartists)) ? $data->topartists : null;
    }

}

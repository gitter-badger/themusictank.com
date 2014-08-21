<?php

namespace App\Model\Api;

use Cake\Core\Configure;
use App\Model\Entity\Artist;
use Cake\Network\Http\Client;
use Cake\Collection\Collection;
use Cake\Utility\Hash;

class LastfmApi {

    private function _postRequest($params = [])
    {
        $params["api_key"] = Configure::read("Apis.lastfm.key");
        $params["format"] = "json";

        $http = new Client();
        $results = $http->get('http://ws.audioscrobbler.com/2.0/', $params);

        if ($results->isOk())
        {
            return json_decode($results->body(), true);
        }

        return null;
    }

    public function getArtistTopAlbums(Artist $artist)
    {
        $data = $this->_postRequest([
            "method" => "artist.gettopalbums",
            "artist" => $artist->name
        ]);

        return Hash::extract($data, "topalbums.album.{n}");
    }


/*
    public function getLastFmTrackDetails($model, $trackName, $artistName)
    {
        $data = $this->_post(array("method" => "track.getinfo", "artist" => $artistName, "track" => $trackName));
        return (!is_null($data) && property_exists($data, "track") && $data->track) ? $data->track : null;
    }

    public function getLastFmAlbumDetails($model, $artistName, $albumName)
    {
        $data = $this->_post(array("method" => "album.getinfo", "artist" => $artistName, "album" => $albumName));
        return (!is_null($data) && property_exists($data, "album") && $data->album) ? $data->album : null;
    }

    public function cleanLastFmWikiText($model, $text)
    {
        $text = trim($text);
        $text = preg_replace('/Read more about .* on .* /', '', $text);
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
        return (!is_null($data) && property_exists($data, "artist") && $data->artist) ? $data->artist : null;
    }

    public function getArtistTopAlbums($model, $artistName)
    {
        $data = $this->_post(array("method" => "artist.gettopalbums", "artist" => $artistName));
        return (property_exists($data, "topalbums") && $data->topalbums && property_exists($data->topalbums, "album")) ? $data->topalbums->album : null;
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
    }*/

}




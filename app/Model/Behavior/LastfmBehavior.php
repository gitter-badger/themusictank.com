<?php

class LastfmBehavior extends ModelBehavior {       
    
    private function _post($params)
    {
        $config = Configure::read("LastFmApiConfig");
        $params["api_key"] = $config["key"];
        $params["format"] = "json";
                
        $curl = curl_init('http://ws.audioscrobbler.com/2.0/?'. http_build_query($params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $body = curl_exec($curl);
        curl_close($curl);    
        return json_decode($body);
    }  
        
    public function getLastFmTrackDetails($model, $trackName, $artistName)
    {
        $data = $this->_post(array("method" => "track.getinfo", "artist" => $artistName, "track" => $trackName));
        return ($data->track) ? $data->track : null;     
    }
     
    public function getLastFmAlbumDetails($model, $artistName, $albumName)
    {
        $data = $this->_post(array("method" => "album.getinfo", "artist" => $artistName, "album" => $albumName));        
        return ($data->album) ? $data->album : null;    
    }
    
    public function cleanLastFmWikiText($model, $text)
    {
        $text = preg_replace('/\n.*\n.*\n/', "\n", $text);
        $text = preg_replace('/\n.*\n/', "\n", $text);
        $text = preg_replace('/Read more about .* on .*/', '', $text);        
        $text = trim(strip_tags($text));        
        
        return str_replace("\n", "</p>\n<p>", '<p>'.$text.'</p>');
    }    
 
    public function getArtistBiography($model, $artistName)
    {
        $data = $this->_post(array("method" => "artist.getinfo", "artist" => $artistName));
        return ($data->artist) ? $data->artist : null;    
    }
    
    public function getArtistTopAlbums($model, $artistName)
    {
        $data = $this->_post(array("method" => "artist.gettopalbums", "artist" => $artistName));
        return ($data->topalbums) ? $data->topalbums->album : null;     
    }    
}

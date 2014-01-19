<?php

/**
 * Description of EchonestApiComponent
 *
 * @author ffaubert
 */
class LastfmApiComponent extends Component {
	
	public function initialize(Controller $controller)
	{
		$this->_controller = $controller;
	}
    
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
    
    public function getArtistBiography($artistName)
    {
        $data = $this->_post(array("method" => "artist.getinfo", "artist" => $artistName));
        return ($data->artist) ? $data->artist : null;                
    }
    
    public function getArtistTopAlbums($artistName)
    {
        $data = $this->_post(array("method" => "artist.gettopalbums", "artist" => $artistName));
        return ($data->topalbums) ? $data->topalbums->album : null;     
    }
    
    public function getAlbumDetails($albumName, $artistName)
    {
        $data = $this->_post(array("method" => "album.getinfo", "artist" => $artistName, "album" => $albumName));
        return ($data->album) ? $data->album : null;     
    }
    
    public function getTrackDetails($trackName, $artistName)
    {
        $data = $this->_post(array("method" => "track.getinfo", "artist" => $artistName, "track" => $trackName));
        return ($data->track) ? $data->track : null;     
    }
    
}

?>

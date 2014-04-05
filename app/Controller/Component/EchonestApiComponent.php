<?php

/**
 * Description of EchonestApiComponent
 *
 * @author ffaubert
 */
class EchonestApiComponent extends Component {
	
	public function initialize(Controller $controller)
	{
		$this->_controller = $controller;
	}
    
    private function _post($url, $params)
    {
        $curl = curl_init($url .'?'. http_build_query($params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $body = curl_exec($curl);
        curl_close($curl);    
        return json_decode($body);
    }    
    
    public function getArtistDescription($artistName)
    {
        $config = Configure::read("EchonestApiConfig");
        $url = "http://developer.echonest.com/api/v4/artist/biographies";        
        $params = array(
            "api_key" => $config[0],
            "format" => "json",
            "name" => $artistName,
            "start" => 0,
            "license" => "cc-by-sa");
        
        $data = $this->_post($url, $params);
        
        if(($data && $data->response->status->message === "Success")) {
            
            foreach($data->response->biographies as $bio)
            {
                if($bio->site === "last.fm")
                {
                    return $bio;
                }
            }
        }
        
        return null;
    }  
}

?>

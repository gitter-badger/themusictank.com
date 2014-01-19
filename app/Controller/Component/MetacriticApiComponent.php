<?php

/**
 * Description of MetacriticApiComponent
 *
 * @author ffaubert
 */
class MetacriticApiComponent extends Component {
	
	public function initialize(Controller $controller)
	{
		$this->_controller = $controller;
	}
    
    private function _post($subfolders)
    {        
        $curl = curl_init('http://www.metacritic.com/music/');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $body = curl_exec($curl);
        curl_close($curl);    
        return $body;
    }    
    
    public function getAlbumScore($albumTitle, $artistName)
    {
        try
        {
            $doc = new DOMDocument();
            @$doc->loadHTMLFile('http://www.metacritic.com/music/' . $albumTitle . '/' . $artistName);

            foreach($doc->getElementsByTagName("div") as $div)
            {
                if(preg_match('/^metascore_w xlarge/', $div->getAttribute("class")))
                {
                    foreach($div->getElementsByTagName("span") as $span)
                    {
                        return (int)$span->nodeValue;
                    }
                }
            }
        }
        catch (Exception $ex)
        {}
        
        return null;
        
    }
}
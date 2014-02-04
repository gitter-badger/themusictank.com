<?php

/**
 * Description of MetacriticApiComponent
 *
 * @author ffaubert
 */
class MetacriticApiComponent extends Component {
	
    const METACRITIC_ROOT_URL = "http://www.metacritic.com/music/";
    
	public function initialize(Controller $controller)
	{
		$this->_controller = $controller;
	}
        
    public function getAlbumScore($albumTitle, $artistName)
    {
        try
        {
            $doc = new DOMDocument();
            @$doc->loadHTMLFile(sprintf("%s/%s/%s", self::METACRITIC_ROOT_URL, $albumTitle, $artistName));

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
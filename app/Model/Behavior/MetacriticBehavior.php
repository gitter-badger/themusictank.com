<?php

class MetacriticBehavior extends ModelBehavior {       
   
    public function getMetacriticScore($model, $albumTitle, $artistName)
    {
        try
        {
            $doc = new DOMDocument();
            @$doc->loadHTMLFile('http://www.metacritic.com/music/' . $this->_toMetacriticLabel($albumTitle) . '/' . $this->_toMetacriticLabel($artistName));

            foreach($doc->getElementsByTagName("div") as $div)
            {
                if(preg_match('/^metascore_w xlarge/', $div->getAttribute("class")))
                {
                    foreach($div->getElementsByTagName("span") as $span)
                    {
                        // By convention, all TMT percentages are smaller than 1.
                        return (int)$span->nodeValue / 100;
                    }
                }
            }
            
            return null;
        }
        catch (Exception $ex) {
            return null;
        }
    }
        
    private function _toMetacriticLabel($string)
    {
        return strtolower(Inflector::slug($string,'-'));
    }
}
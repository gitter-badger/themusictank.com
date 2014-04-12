<?php
class OEmbedable extends AppModel
{
    public function toOEmbed($data)
    {                
        $serverUrl = $_SERVER['SERVER_NAME'];
        $slug = $this->getData($this->alias.".slug");
        $iframeUrl = sprintf("http://%s/%ss/embed/%s/", $serverUrl, strtolower($this->alias), $slug);
        $url = sprintf("http://%s/%ss/view/%s/", $serverUrl, strtolower($this->alias), $slug);
        return array(          
            "version"   => "1.0",
            "type"      => "rich",
            "provider_name" => "The Music Tank",
            "provider_url"  => sprintf("http://%s/", $serverUrl),
            "url"       => $url,
            "title"     => $this->getData($this->alias.".title"),
            "data"      => $data,
            "width"     => 500,
            "height"    => 350,
            "html"      => '<iframe width="500" height="350" src="'.$iframeUrl.'" frameborder="0"></iframe>'
        );
    }
    
    public function getOEmbedUrl()
    {
        $serverUrl = $_SERVER['SERVER_NAME'];
        $destination = sprintf("http://%s/%ss/view/%s/", $serverUrl, strtolower($this->alias), $this->getData($this->alias.".slug"));
        return sprintf("http://%s/oembed?url=%s", $serverUrl, urlencode($destination));
    }
}
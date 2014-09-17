<?php
namespace App\Model\Entity;

use Cake\Utility\Hash;

trait OembedableTrait {

    public function toOEmbed($additionalData = [])
    {
        $className  = $this->_getFormattedClassName();
        $iframeUrl  = sprintf("http://%s/%ss/embed/%s/", $_SERVER['SERVER_NAME'], $className, $this->slug);
        $url        = sprintf("http://%s/%ss/view/%s/", $_SERVER['SERVER_NAME'], $className, $this->slug);

        return json_encode([
            "version"   => "1.0",
            "type"      => "rich",
            "provider_name" => "The Music Tank",
            "url"       => $url,
            "title"     => $this->name ? $this->name : $this->title,
            "width"     => 500,
            "height"    => 350,
            "html"      => '<iframe width="500" height="350" src="'.$iframeUrl.'" frameborder="0"></iframe>',
            "data"      => ["$className" => $this]
        ]);
    }

    public function getOembedUrl()
    {
        $serverUrl = $_SERVER['SERVER_NAME'];
        $destination = sprintf("http://%s/%ss/view/%s/", $serverUrl, $this->_getFormattedClassName(), $this->slug);
        return sprintf("http://%s/oembed?url=%s", $serverUrl, urlencode($destination));
    }

    private function _getFormattedClassName()
    {
        $namespace = explode('\\', strtolower(get_class($this)));
        return array_pop($namespace);
    }

}

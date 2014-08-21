<?php
namespace App\Model\Entity;

trait OembedableTrait {

    public function toOEmbed($model, $data)
    {
        $serverUrl = $_SERVER['SERVER_NAME'];
        $slug = Hash::get($data, $model->alias.".slug");
        $iframeUrl = sprintf("http://%s/%ss/embed/%s/", $serverUrl, strtolower($model->alias), $slug);
        $url = sprintf("http://%s/%ss/view/%s/", $serverUrl, strtolower($model->alias), $slug);

        return array(
            "version"   => "1.0",
            "type"      => "rich",
            "provider_name" => "The Music Tank",
            "provider_url"  => sprintf("http://%s/", $serverUrl),
            "url"       => $url,
            "title"     => $model->title,
            "data"      => $data,
            "width"     => 500,
            "height"    => 350,
            "html"      => '<iframe width="500" height="350" src="'.$iframeUrl.'" frameborder="0"></iframe>'
        );
    }

    public function getOembedUrl()
    {
        $serverUrl = $_SERVER['SERVER_NAME'];
        $destination = sprintf("http://%s/%ss/view/%s/", $serverUrl, strtolower($this->alias), $this->slug);
        return sprintf("http://%s/oembed?url=%s", $serverUrl, urlencode($destination));
    }

}

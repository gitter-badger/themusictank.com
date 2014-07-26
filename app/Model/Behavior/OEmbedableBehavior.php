<?php

class OEmbedableBehavior extends ModelBehavior {

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
            "title"     => Hash::get($data, $model->alias.".title"),
            "data"      => $data,
            "width"     => 500,
            "height"    => 350,
            "html"      => '<iframe width="500" height="350" src="'.$iframeUrl.'" frameborder="0"></iframe>'
        );
    }

    public function getOEmbedUrl($model)
    {
        $serverUrl = $_SERVER['SERVER_NAME'];
        $destination = sprintf("http://%s/%ss/view/%s/", $serverUrl, strtolower($model->alias), $model->getData($model->alias.".slug"));
        return sprintf("http://%s/oembed?url=%s", $serverUrl, urlencode($destination));
    }

}

<?php

class MetaTagsHelper extends AppHelper {

    var $helpers = array('Html');
    // Metas are values that can be printed right on the page
    private $_metas = null;
    private $_scripts = null;
    // Layout metas are array of information that need to be converted into regular meta
    private $_layoutMetas = null;

    public function init()
    {
        $domain = 'http://' . $_SERVER['SERVER_NAME'] . '/';

        $metas = array(
            $this->Html->charset(),
            $this->Html->meta(array("name" => 'viewport', "value" => "width=device-width, initial-scale=1.0")),
            $this->Html->meta('favicon.ico','/img/favicon.ico', array('type' => 'icon')),
            $this->Html->meta(array("name" => 'referrer', "value" => "origin")),
            $this->Html->meta('canonical', $this->Html->url( null, true ), array('rel'=>'canonical', 'type'=>null, 'title'=>null)),
            '<link href="https://plus.google.com/117543200043480372792" rel="publisher" />',
            '<link rel="author" type="text/plain" href="'.$domain.'humans.txt" />',
            '<meta name=viewport content="width=device-width, initial-scale=1">',
            '<noscript><meta http-equiv="refresh" content="0; URL=/pages/requirements/" /></noscript>',
            $this->Html->css(array(
                "styles.min"
            ))
        );

        $scripts = array(
            $this->Html->script(array(
                "vendor/jquery-2.1.1.min",
                "vendor/bootstrap.min",
                "vendor/d3.v3.min",
                "vendor/typeahead",
                "vendor/jquery.easing-1.3",
                "vendor/jquery.royalslider.min",
                "tmt"
            ))
        );

        $this->_metas = $metas;
        $this->_scripts = $scripts;
    }

    public function add($metas)
    {
        foreach($metas as $meta)
        {
            $this->_metas[] = $meta;
        }
    }

    public function addLayoutMeta($metas)
    {
        $this->_layoutMetas = $metas;

        foreach($metas as $key =>$meta)
        {
            $this->_metas[] = $this->Html->meta($key, $meta);
        }
    }

    public function compileScripts()
    {
        return implode("\n\t", array_filter($this->_scripts));
    }

    public function compileMetas()
    {
        if(isset($this->_layoutMetas))
        {
            $this->_buildOpenGraphTags();
            $this->_buildTwitterTags();
        }

        return implode("\n\t", array_filter($this->_metas));
    }
/*
    public function addPlayerMeta($preferredPlayer)
    {
        switch($preferredPlayer)
        {
            case "rdio" : $playerScript = array('vendor/swf/swfobject', 'vendor/swf/flash_detect_min.js', 'player/reviewer', 'player/rdio'); break;
            case "mp3" : $playerScript = array('vendor/id3/id3-minimized', 'player/reviewer', 'player/mp3'); break;
        }

        $this->_metas[] = $this->Html->script(array_merge(array('//code.jquery.com/ui/1.10.3/jquery-ui.js', 'vendor/animation/RequestAnimationFrame', 'player/player', 'player/graph'), $playerScript));

        if(isset($isReview) && $isReview)
        {
            $this->_metas[] = $this->Html->script('player/reviewer');
        }
    }*/

    public function addOEmbedMeta($oembedLink)
    {
        $this->_metas[] = '<link rel="alternate" type="application/json+oembed" href="'.$oembedLink.'" title="oEmbed Profile" />';
    }

    public function _buildOpenGraphTags()
    {
        $this->_metas[] = $this->Html->meta(array("name" => "og:url",       "description" => $this->Html->url( null, true )));
        $this->_metas[] = $this->Html->meta(array("name" => "og:image",     "description" => "http://" . $_SERVER['SERVER_NAME'] . "/img/social-share.png"));
        $this->_metas[] = $this->Html->meta(array("name" => "og:site_name", "description" => "The Music Tank"));
        $this->_metas[] = $this->Html->meta(array("name" => "og:type",      "description" => "website"));
        $this->_metas[] = $this->Html->meta(array("name" => "og:locale",    "description" => "en_CA"));

        foreach(array("title", "description") as $type)
        {
            if(array_key_exists($type, $this->_layoutMetas))
            {
                $this->_metas[] = $this->Html->meta(array("name" => "og:".$type, "description" => $this->_layoutMetas[$type]));
            }
        }
    }

    public function _buildTwitterTags()
    {
        $this->_metas[] = $this->Html->meta(array("name" => "twitter:card",     "description" => "summary"));
        $this->_metas[] = $this->Html->meta(array("name" => "twitter:image",    "description" => "http://" . $_SERVER['SERVER_NAME'] . "/img/social-share.png"));

        foreach(array("title", "description") as $type)
        {
            if(array_key_exists($type, $this->_layoutMetas))
            {
                $this->_metas[] = $this->Html->meta(array("name" => "twitter:".$type, "description" => $this->_layoutMetas[$type]));
            }
        }
    }

}

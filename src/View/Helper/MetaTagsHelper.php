<?php

namespace App\View\Helper;

use App\View\Helper\AppHelper;
use Cake\Routing\Router;
use Cake\Core\Configure;

class MetaTagsHelper extends AppHelper {

    public $helpers = ['Html'];

    // Metas are values that can be printed right on the page
    private $_metas = null;
    private $_scripts = null;

    public function init()
    {
        $domain = 'http://' . $_SERVER['SERVER_NAME'] . '/';

        $metas = [
            $this->Html->charset(),
            $this->Html->meta('favicon.ico','/img/favicon.ico', ['type' => 'icon']),
            $this->Html->meta('canonical', Router::url( null, true ), ['rel'=>'canonical', 'type'=>null, 'title'=>null]),
            '<link href="https://plus.google.com/117543200043480372792" rel="publisher" />',
            '<link rel="author" type="text/plain" href="'.$domain.'humans.txt" />',
            '<noscript><meta http-equiv="refresh" content="0; URL=/pages/requirements/" /></noscript>',
            '<meta name="viewport" content="width=device-width, initial-scale=1">',
            '<meta name="referrer" value="origin">',
            '<meta name="og:url" description="'.Router::url( null, true ).'" />',
            '<meta name="og:image" description="'.$domain.'img/social-share.png" />',
            '<meta name="og:site_name" description="The Music Tank" />',
            '<meta name="og:type" description="website" />',
            '<meta name="og:locale" description="en_CA" />',
            '<meta name="twitter:card" description="summary" />',
            '<meta name="twitter:image" description="'.$domain.'img/social-share.png" />',
            $this->Html->css(["vendor.min", "tmt.min"])
        ];

        //$scripts = [$this->Html->script(["vendor.min", "tmt.min"])];
        $scripts = [$this->Html->script(["vendor.min", "tmt"])];

        $this->_metas = $metas;
        $this->_scripts = $scripts;
    }

    public function addPageMeta(array $metas)
    {
        if(array_key_exists("title", $metas)) {
            $parsedTitle = $this->_parseTitle($metas["title"]);
            array_unshift($this->_metas, "<title>". $parsedTitle ."</title>");
            $this->_metas[] = '<meta name="og:title" description="'.h($parsedTitle).'" />';
            $this->_metas[] = '<meta name="twitter:title" description="'.h($parsedTitle).'" />';
        }

        if(array_key_exists("keywords", $metas)) {
            $this->_metas[] = $this->Html->meta("keywords", $metas["keywords"]);
        }

        if(array_key_exists("description", $metas)) {
            $this->_metas[] = $this->Html->meta("description", $metas["description"]);
            $this->_metas[] = '<meta name="og:description" description="'.h($metas["description"]).'" />';
            $this->_metas[] = '<meta name="twitter:description" description="'.h($metas["description"]).'" />';
        }

        if(array_key_exists("oembedLink", $metas)) {
            $this->_metas[] = '<link rel="alternate" type="application/json+oembed" href="'.h($metas['oembedLink']).'" title="oEmbed Profile" />';
        }
    }

    public function compileScripts()
    {
        return implode("\n\t", array_filter($this->_scripts));
    }

    public function compileMetas()
    {
        return implode("\n\t", $this->_metas);
    }

    private function _parseTitle($titles)
    {
        $title      = "The Music Tank";
        $separator  = " &mdash; ";

        if(gettype($titles) === gettype("string")) {
            $titles = [$titles];
        }

        return implode($separator, $titles) . $separator . $title;
    }

}
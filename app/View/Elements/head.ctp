<?php
    $this->MetaTags->init();
    $this->MetaTags->add(array(
        $this->fetch('meta'),
        $this->fetch('css'),        
        $this->fetch('script')
    ));
    
    if(isset($meta_for_layout))
    {
        $this->MetaTags->addLayoutMeta($meta_for_layout);
    }
    
    
    if(!isset($customMetas)) $customMetas = array();
    
    if(isset($preferredPlayer))
    {     
        switch($preferredPlayer)
        {
            case "rdio" : $playerScript = array('lib/swf/swfobject', 'player/rdio'); break;            
            case "mp3" : $playerScript = array('lib/id3/id3-minimized', 'player/mp3'); break;
        }
        
        $customMetas[] = $this->Html->script(array_merge(array('//code.jquery.com/ui/1.10.3/jquery-ui.js', 'lib/animation/RequestAnimationFrame', 'player/player', 'player/graph'), $playerScript));
    }    
    
    if(isset($oembedLink))
    {
         $customMetas[] =  '<link rel="alternate" type="application/json+oembed" href="'.$oembedLink.'" title="oEmbed Profile" />';
    }    
    
    if(isset($customMetas) && count($customMetas))
    {
        $this->MetaTags->add($customMetas);
    }
    
    echo $this->MetaTags->compile();
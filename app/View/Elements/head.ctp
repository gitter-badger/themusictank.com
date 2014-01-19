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
    
    if(isset($customMetas))
    {
        $this->MetaTags->add($customMetas);
    }
    
    echo $this->MetaTags->compile();
<!DOCTYPE html>
<html>
<head>    
	<title><?php echo ($title_for_layout) ? $title_for_layout . " &mdash; " : ""; ?><?php echo __("The Music Tank"); ?></title>
    <?php 
        $this->MetaTags->init();
        if(isset($meta_for_layout)) $this->MetaTags->addLayoutMeta($meta_for_layout);
        if(isset($preferredPlayer)) $this->MetaTags->addPlayerMeta($preferredPlayer);
        if(isset($oembedLink))      $this->MetaTags->addOEmbedMeta($oembedLink);
        if(isset($customMetas))     $this->MetaTags->add($customMetas);
        echo $this->MetaTags->compile();
     ?>
</head>
<body>
    <?php echo $this->fetch('content'); ?>
    <?php echo $this->element('analytics'); ?>    
    <?php echo $this->element('sql_dump'); ?> 
</body>
</html>
    
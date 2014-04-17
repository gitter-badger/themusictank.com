<!DOCTYPE html>
<html>
<head>    
	<title><?php echo $this->App->getTitle($title_for_layout); ?></title>
    <?php 
        $this->MetaTags->init();
        if(isset($meta_for_layout)) $this->MetaTags->addLayoutMeta($meta_for_layout);
        if(isset($preferredPlayer)) $this->MetaTags->addPlayerMeta($preferredPlayer);
        if(isset($oembedLink))      $this->MetaTags->addOEmbedMeta($oembedLink);
        if(isset($customMetas))     $this->MetaTags->add($customMetas);
        echo $this->MetaTags->compile();
     ?>
</head>
<body class="<?php echo $this->App->contextToClassNames(); ?>">

    <?php echo $this->element('header'); ?>
    
    <section class="site-main">
        <?php echo $this->Session->flash(); ?>
        <?php echo $this->fetch('content'); ?>
    </section>
    
    <?php echo $this->element('foot'); ?>
    
    <?php echo $this->element('analytics'); ?>    
    <?php echo $this->element('sql_dump'); ?> 
</body>
</html>
    
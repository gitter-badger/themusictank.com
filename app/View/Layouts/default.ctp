<!DOCTYPE html>
<html>
<head>    
	<title><?php echo ($title_for_layout) ? $title_for_layout . " &mdash; " : ""; ?><?php echo __("The Music Tank"); ?></title>
    <?php echo $this->element('head'); ?>
</head>
<body class="<?php echo sprintf("%s %s %s", $this->params["controller"], $this->params["action"], implode(" ", $this->params["pass"])); ?>">

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
    
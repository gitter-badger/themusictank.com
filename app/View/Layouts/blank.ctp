<!DOCTYPE html>
<html>
<head>    
	<title><?php echo ($title_for_layout) ? $title_for_layout . " &mdash; " : ""; ?><?php echo __("The Music Tank"); ?></title>
    <?php echo $this->element('head'); ?>
</head>
<body>
    <?php echo $this->fetch('content'); ?>
    <?php echo $this->element('analytics'); ?>    
    <?php echo $this->element('sql_dump'); ?> 
</body>
</html>
    
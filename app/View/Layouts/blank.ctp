<!DOCTYPE html>
<html>
<head>    
	<title><?php echo __("The Music Tank"); ?></title>
</head>
<body>
    <?php echo $this->fetch('content'); ?>
    <?php echo $this->element('analytics'); ?>    
    <?php echo $this->element('sql_dump'); ?> 
</body>
</html>
    
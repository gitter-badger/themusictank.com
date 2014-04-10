
<h2><?php echo __("Artists"); ?></h2>

<?php debug($artists); ?>

<?php if(count($artists) > 5) : ?>
	<?php echo $this->Html->link(__("View more"), array('controller' => 'artists', 'action' => 'search', '?' => array("name" => $query))); ?>          
<?php endif; ?>

<h2><?php echo __("Ablums"); ?></h2>

<?php debug($albums); ?>

<?php if(count($albums) > 2) : ?>
	<?php echo $this->Html->link(__("View more"), array('controller' => 'albums', 'action' => 'search', '?' => array("name" => $query))); ?>          
<?php endif; ?>

<h2><?php echo __("Tracks"); ?></h2>

<?php debug($tracks); ?>

<?php if(count($tracks) > 5) : ?>
	<?php echo $this->Html->link(__("View more"), array('controller' => 'tracks', 'action' => 'search', '?' => array("title" => $query))); ?>          
<?php endif; ?>
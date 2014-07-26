<nav class="sub-menu">
	<div class="container container-fluid">
		<div class="row">
		    <ol class="breadcrumb">
		        <li class="active"><?php echo $this->Html->link(__("Search"), array('controller' => 'search', 'action' => 'index')); ?></li>
		    </ol>
	    </div>
    </div>
</nav>
<div class="container container-fluid">

	<h2><?php echo __("Search Results"); ?></h2>

	<h3><?php echo __("Artists"); ?></h3>
	<?php if(count($artists) > 0): ?>

	    <?php echo $this->element('artistTiledList', array("artists" => array_slice($artists, 0, $maxResults))); ?>

		<?php if(count($artists) >= $maxResults) : ?>
			<?php echo $this->Html->link(__("View more"), array('controller' => 'artists', 'action' => 'search', '?' => array("name" => $query))); ?>
		<?php endif; ?>

	<?php else : ?>
		<p><?php echo __("Search returned no results."); ?></p>
	<?php endif; ?>

	<h3><?php echo __("Ablums"); ?></h3>
	<?php if(count($albums) > 0): ?>
	    <?php echo $this->element('albumTiledList', array("albums" => array_slice($albums, 0, $maxResults))); ?>

		<?php if(count($albums) >= $maxResults) : ?>
			<?php echo $this->Html->link(__("View more"), array('controller' => 'albums', 'action' => 'search', '?' => array("name" => $query))); ?>
		<?php endif; ?>

	<?php else : ?>
		<p><?php echo __("Search returned no results."); ?></p>
	<?php endif; ?>

	<h3><?php echo __("Tracks"); ?></h3>
	<?php if(count($tracks) > 0): ?>
	    <?php echo $this->element('trackTiledList', array("tracks" => array_slice($tracks, 0, $maxResults))); ?>

		<?php if(count($albums) >= $maxResults) : ?>
			<?php echo $this->Html->link(__("View more"), array('controller' => 'tracks', 'action' => 'search', '?' => array("name" => $query))); ?>
		<?php endif; ?>

	<?php else : ?>
		<p><?php echo __("Search returned no results."); ?></p>
	<?php endif; ?>

</div>

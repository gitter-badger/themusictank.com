<?php if(count($albums) > 0) : ?>
    <?php echo $this->element('albumTiledList', array("albums" => $albums)); ?>
<?php else : ?>
	<p><?php echo __("We cannot load the discography at this time."); ?></p>
<?php endif; ?>	
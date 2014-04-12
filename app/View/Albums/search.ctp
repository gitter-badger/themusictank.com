<h2><?php echo $title; ?></h2>

<section class="search album-search">
    <header>
        <h3><?php echo __('Find an album'); ?></h3>
    </header>

    <?php echo $this->Form->create(array('action' => 'search', 'type' => 'get')); ?>
    <?php echo $this->Form->input('name', array('label' => __('Name'))); ?>
    <?php echo $this->Form->end(__("Search")); ?>    
</section>

<?php if(count($albums) > 0): ?>
    <?php echo $this->element('albumTiledList', array("albums" => $albums)); ?>
<?php else : ?>
	<p><?php echo __("Search returned no results."); ?></p>
<?php endif; ?>
    
<div class="pagination">
    <?php echo $this->Paginator->numbers(); ?>
    <?php echo __("Page"); ?> <?php echo $this->Paginator->counter(); ?>
</div>
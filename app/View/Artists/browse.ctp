<h2><?php echo $title; ?></h2>

<section class="search">
    <?php echo $this->element('artistSearch', array("artistCategories" => $artistCategories)); ?>
</section>

<?php echo $this->element('artistTiledList', array("artists" => $artists)); ?>
    
<div class="pagination">
    <?php echo $this->Paginator->numbers(); ?>
    <?php echo __("Page"); ?> <?php echo $this->Paginator->counter(); ?>
</div>
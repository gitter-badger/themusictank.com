<section class="artist-search">
    <header>
        <h3><?php echo __('Find artist'); ?></h3>
    </header>
    <?php echo $this->Form->create(array('action' => 'search', 'type' => 'get')); ?>
    <?php echo $this->Form->input('name', array('label' => __('Name'))); ?>
    <?php echo $this->Form->end(__("Search")); ?>
    
    <ul class="tiled-list artist-categories">
    <?php foreach($artistCategories as $category) : ?>
        <li>
            <?php echo $this->Html->link($category, array('controller' => 'artists', 'action' => 'browse',  strtolower($category))); ?>
        </li>
    <?php endforeach; ?>
    </ul>    
</section>
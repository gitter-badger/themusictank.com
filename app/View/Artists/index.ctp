<section class="browse-by-letter">
    <header>
        <h2><?php echo __('Browse alphabetically'); ?></h2>
    </header>

    <div class="row tiled-list artist-categories">
    <?php foreach($artistCategories as $category) : ?>
        <div class="col-xs-1 col-md-2">
            <?php echo $this->Html->link($category, array('controller' => 'artists', 'action' => 'browse',  strtolower($category))); ?>
        </div>
    <?php endforeach; ?>
    </div>    
</section>

<section class="popular-artists">
    <header>
        <h2><?php echo __('Popular right now'); ?></h2>
    </header>
    <?php echo $this->element('artistTiledList', array("artists" => $popularArtists)); ?>
</section>

<section class="new-album-releases">
    <header>
        <h2><?php echo __('New album releases'); ?></h2>
        <?php echo $this->Html->link(__("More"), array('controller' => 'albums', 'action' => 'newreleases'), array("class" => "view-more")); ?>        
    </header>     
    <div class="row tiled-list albums">
        <?php echo $this->element('albumTiledList', array("albums" => $newReleases)); ?>
    </div>
</section>
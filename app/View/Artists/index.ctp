
<?php echo $this->element('artistSearch', array("artistCategories" => $artistCategories)); ?>


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
    <ul class="tiled-list albums">
        <?php foreach($newReleases as $album) :?>
            <li>
                <?php $imgSrc = !is_null($album["Albums"]["image"]) ? $album["Albums"]["image"] : "/img/placeholder.png"; ?>
                <?php echo $this->Html->link(
                            $this->Html->image($imgSrc, array("alt" => $album["Albums"]["name"], "class" => "thumbnail")),
                            array('controller' => 'albums', 'action' => 'view', $album["Albums"]["slug"]),
                            array('escape' => false)
                    ); ?>                    
                <time datetime="<?php echo date("c", $album["Albums"]["release_date"]); ?>"><?php echo date("F j Y", $album["Albums"]["release_date"]); ?></time>
                <h3><?php echo $this->Html->link($album["Albums"]["name"], array('controller' => 'albums', 'action' => 'view', $album["Albums"]["slug"])); ?></h3>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
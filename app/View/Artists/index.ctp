
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
                <a class="thumbnail" href="<?php echo $this->Html->url(array('controller' => 'albums', 'action' => 'view', $album["Albums"]["slug"])); ?>" <?php if(isset($album["Albums"]["image"])){  echo 'style="background-image:url(/img/'.$album["Albums"]["image"].');"'; } ?>>
                    &nbsp;
                </a>                
                <time datetime="<?php echo date("c", $album["Albums"]["release_date"]); ?>"><?php echo date("F j Y", $album["Albums"]["release_date"]); ?></time>
                <h3>
                    <?php echo $this->Html->link($album["Albums"]["name"], array('controller' => 'albums', 'action' => 'view', $album["Albums"]["slug"])); ?> <br />
                    <?php echo __("by"); ?> <?php echo $this->Html->link($album["Artist"]["name"], array('controller' => 'artists', 'action' => 'view', $album["Artist"]["slug"])); ?>
                </h3>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
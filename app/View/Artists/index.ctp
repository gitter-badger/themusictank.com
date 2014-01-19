
<section class="popular-artists">
    <header>
        <h2><?php echo __('Popular right now'); ?></h2>
    </header>
    <ul class="tiled-list artists">
    <?php foreach($popularArtists as $artist) : ?>
        <li>
            <?php echo isset($artist["LastfmArtist"]["image"]) ? $this->Html->image($artist["LastfmArtist"]["image"], array("alt" => $artist["Artist"]["name"], "class" => "thumbnail")) : ""; ?>
            <?php echo $this->Html->link($artist["Artist"]["name"], array('controller' => 'artists', 'action' => 'view', $artist["Artist"]["slug"])); ?>
            <?php if(count($artist["Albums"])) : ?>
            <ul class="recent-albums">
                <?php foreach($artist["Albums"] as $i => $album) : if($i >= 3) break;?>
                <li>
                    <?php if(isset($album["image"])) : ?>
                    <?php echo $this->Html->image($album["image"], array("alt" => $album["name"], "class" => "thumbnail", "height" => 50)); ?>
                    <?php endif; ?>
                    <?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ul>
</section>

<section class="new-album-releases">
    <header>
        <h2><?php echo __('New album releases'); ?></h2>
    </header>
    <ul class="tiled-list albums">
        <?php  foreach($newReleases as $album) : ?>
            <li>
                <?php if(!is_null($album["Album"]["image"])) : ?>
                    <?php echo $this->Html->link(
                            $this->Html->image($album["Album"]["image"], array("alt" => $album["Album"]["name"], "class" => "thumbnail", "width" => 200, "height" => 200)),
                            array('controller' => 'albums', 'action' => 'view', $album["Album"]["slug"]),
                            array('escape' => false)
                    ); ?>
                <?php endif; ?>
                <p><?php echo $this->Html->link($album["Album"]["name"], array('controller' => 'albums', 'action' => 'view', $album["Album"]["slug"])); ?></p>
                <p><?php echo __("Released"); ?> <?php echo date("F j Y", $album["Album"]["release_date"]); ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
    <footer>
        <p><?php echo $this->Html->link(__("More"), array('controller' => 'albums', 'action' => 'newreleases')); ?></p>
    </footer>
</section>

<section class="search">
    <header>
        <h2><?php echo __('Browse available artists'); ?></h2>
    </header>
    <?php echo $this->element('artistSearch', array("artistCategories" => $artistCategories)); ?>
</section>

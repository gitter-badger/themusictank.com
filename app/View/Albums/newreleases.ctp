<section class="new-releases">
    <header>
        <h3><?php echo __("New album releases") ?></h3>
        <p><?php echo __("For the week of") . " " . $forTheWeekOf; ?></p>
    </header>
    <?php if(count($newReleases) > 0) : ?>
        <ul class="tiled-list albums">
        <?php foreach($newReleases as $album) : ?>
            <li>
                <div class="thumbnail">
                <?php if(!is_null($album["Album"]["image"])) : ?>
                    <?php echo $this->Html->link(
                            $this->Html->image($album["Album"]["image"], array("alt" => $album["Album"]["name"], "class" => "thumbnail", "width" => 200, "height" => 200)),
                            array('controller' => 'albums', 'action' => 'view', $album["Album"]["slug"]),
                            array('escape' => false)
                    ); ?>
                <?php endif; ?>
                </div>
                <h3><?php echo $this->Html->link($album["Album"]["name"], array('controller' => 'albums', 'action' => 'view', $album["Album"]["slug"])); ?></h3>
                <p><?php echo __("By"); ?> <?php echo $this->Html->link($album["Artist"]["name"], array('controller' => 'artists', 'action' => 'view', $album["Artist"]["slug"])); ?></p>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p><?php echo __("No new releases have been found"); ?>.</p>
    <?php endif; ?>        
</section>
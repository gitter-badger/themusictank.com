 <ul class="tiled-list albums">
    <?php foreach($albums as $album) : ?>
        <li>
            <div class="thumbnail">
            <?php if(!is_null($album["Album"]["image"])) : ?>
                <?php echo $this->Html->link(
                        $this->Html->image($album["Album"]["image"], array("alt" => $album["Album"]["name"], "class" => "thumbnail")),
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
 <div class="row tiled-list albums">
    <?php foreach($albums as $album) : ?>
        <div class="col-xs-4 col-md-4">
            <div class="thumbnail">
                <?php echo $this->Html->link(
                        $this->Html->image($this->App->getImageUrl($album["Album"]["image"]), array("alt" => $album["Album"]["name"], "class" => "thumbnail")),
                        array('controller' => 'albums', 'action' => 'view', $album["Album"]["slug"]),
                        array('escape' => false)
                ); ?>
            </div>
            <h3><?php echo $this->Html->link($album["Album"]["name"], array('controller' => 'albums', 'action' => 'view', $album["Album"]["slug"])); ?></h3>
            <p><?php echo __("By"); ?> <?php echo $this->Html->link($album["Artist"]["name"], array('controller' => 'artists', 'action' => 'view', $album["Artist"]["slug"])); ?></p>
        </div>
    <?php endforeach; ?>
</ul>
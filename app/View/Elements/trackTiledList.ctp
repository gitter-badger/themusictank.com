 <ul class="tiled-list tracks">
    <?php foreach($tracks as $track) : ?>
        <li>
            <div class="thumbnail">
            <?php if(!is_null($track["Album"]["image"])) : ?>
                <?php echo $this->Html->link(
                        $this->Html->image($track["Album"]["image"], array("alt" => $track["Album"]["name"], "class" => "thumbnail")),
                        array('controller' => 'tracks', 'action' => 'view', $track["Album"]["slug"]),
                        array('escape' => false)
                ); ?>
            <?php endif; ?>
            </div>
            <h3><?php echo $this->Html->link($track["Track"]["title"], array('controller' => 'tracks', 'action' => 'view', $track["Track"]["slug"])); ?></h3>
            <p><?php echo __("Found on"); ?> <?php echo $this->Html->link($track["Album"]["name"], array('controller' => 'albums', 'action' => 'view', $track["Album"]["slug"])); ?></p>
        </li>
    <?php endforeach; ?>
</ul>
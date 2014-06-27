 <div class="row tiles tracks">
    <?php foreach($tracks as $track) : ?>
        <div class="col-xs-6 col-md-3 track">
            <?php echo $this->Html->link(
                $this->Html->image($this->App->getImageUrl($track["Album"]), array("alt" => $track["Album"]["name"])),
                array('controller' => 'tracks', 'action' => 'view', $track["Album"]["slug"]),
                array('escape' => false, "class" => "thumbnail")
            ); ?>
            <h3><?php echo $this->Html->link($track["Track"]["title"], array('controller' => 'tracks', 'action' => 'view', $track["Track"]["slug"])); ?></h3>
            <p><?php echo __("Found on"); ?> <?php echo $this->Html->link($track["Album"]["name"], array('controller' => 'albums', 'action' => 'view', $track["Album"]["slug"])); ?></p>
        </div>
    <?php endforeach; ?>
</div>

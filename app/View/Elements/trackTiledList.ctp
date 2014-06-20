 <div class="row tracks">
    <?php foreach($tracks as $track) : ?>
        <div class="col-xs-6 col-md-3">
            <div class="thumbnail">
                <?php echo $this->Html->link(
                        $this->Html->image($this->App->getImageUrl($track["Album"], true), array("alt" => $track["Album"]["name"], "class" => "thumbnail")),
                        array('controller' => 'tracks', 'action' => 'view', $track["Album"]["slug"]),
                        array('escape' => false)
                ); ?>
            </div>
            <h3><?php echo $this->Html->link($track["Track"]["title"], array('controller' => 'tracks', 'action' => 'view', $track["Track"]["slug"])); ?></h3>
            <p><?php echo __("Found on"); ?> <?php echo $this->Html->link($track["Album"]["name"], array('controller' => 'albums', 'action' => 'view', $track["Album"]["slug"])); ?></p>
        </div>
    <?php endforeach; ?>
</div>

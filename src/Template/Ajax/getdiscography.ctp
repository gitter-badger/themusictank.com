<?php if (count($artist->albums)) : ?>
    <?= $this->element('tiledlists/albums', ['albums' => array_slice($artist->albums, 0, 4)]); ?>
    <?php if (count($artist->albums) > 4) : ?>
        <p><?= $this->Html->link(__("View more"), ['controller' => 'artists', 'action' => 'discography', $artist->slug], ['class' => "btn btn-primary pull-right"]); ?></p>
    <?php endif; ?>
<?php else : ?>
    <p><?php echo __("We cannot load the discography at this time."); ?></p>
<?php endif; ?>

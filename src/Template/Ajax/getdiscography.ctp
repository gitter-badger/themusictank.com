<?php if (count($artist->albums)) : ?>
    <?= $this->element('tiledlists/albums', ['albums' => $artist->albums]); ?>
<?php else : ?>
    <p><?php echo __("We cannot load the discography at this time."); ?></p>
<?php endif; ?>

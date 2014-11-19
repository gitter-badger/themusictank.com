 <div class="details">
    <div class="picture img-circle">
        <?= $this->Html->link("", ['controller' => 'artists', 'action' => 'view', $artist->slug]); ?>
    </div>
    <h1><?= $this->Html->link($artist->name, ['controller' => 'artists', 'action' => 'view', $artist->slug]); ?></h1>

    <div class="score">
        <?php if(is_null($artist->snapshot)) : ?>
            N/A
        <?php else : ?>
            <?= (int)($artist->snapshot->score * 100); ?>%
        <?php endif; ?>
        <span><?= __("Score"); ?></span>
    </div>

    <div class="piechart artist-<?= $artist->id; ?>"></div>
</div>

<?php $this->start('header-extra'); ?>
<?= $this->fetch('header-extra'); ?>
<style type="text/css">
    @media (min-width: 501px) {
        .details .picture a { background-image:url(<?= $artist->getImageUrl(); ?>); }
    }
    @media (max-width: 500px) {
        .details .picture a { background-image:url(<?= $artist->getImageUrl('mobile_thumb'); ?>); }
    }
</style>
<?php $this->end(); ?>


<div class="details">

    <div class="picture secondary img-circle">
        <?= $this->Html->link("", ['controller' => 'artists', 'action' => 'view', $album->artist->slug], ['escape' => false]); ?>
    </div>

    <div class="picture primary">
        <?= $this->Html->link("", ['controller' => 'albums', 'action' => 'view', $album->slug], ['escape' => false]); ?>
    </div>

    <h2><?= $this->Html->link($album->artist->name, ['controller' => 'artists', 'action' => 'view', $album->artist->slug]); ?></h2>
    <h1><?= $this->Html->link($album->name, ['controller' => 'albums', 'action' => 'view', $album->slug]); ?></h1>

    <?php if ($album->hasReleaseDate()) : ?>
        <time datetime="<?= $album->getFormatedReleaseDate("c"); ?>"><?= $album->getFormatedReleaseDate(); ?></time>
    <?php endif; ?>

    <div class="score">
        <?php if(is_null($album->snapshot)) : ?>
            N/A
        <?php else : ?>
            <?= (int)($album->snapshot->score * 100); ?>%
        <?php endif; ?>
        <span><?= __("Score"); ?></span>
    </div>

    <?php if($album->isNotable()) : ?>
        <div class="notable"><?= __("Notable Album"); ?></div>
    <?php endif; ?>

    <div class="piechart album-<?= $album->id; ?>"></div>

</div>

<?php $this->start('header-extra'); ?>
<?= $this->fetch('header-extra'); ?>
<style type="text/css">
    @media (min-width: 501px) {
        .details .primary a { background-image:url(<?= $album->getImageUrl(); ?>); }
        .details .secondary a { background-image:url(<?= $album->artist->getImageUrl(); ?>); }
    }
    @media (max-width: 500px) {
        .details .primary a { background-image:url(<?= $album->getImageUrl('mobile_thumb'); ?>); }
        .details .secondary a { background-image:url(<?= $album->artist->getImageUrl('mobile_thumb'); ?>); }
    }
</style>
<?php $this->end(); ?>

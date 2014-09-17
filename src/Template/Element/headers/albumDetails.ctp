<div class="details">

    <div class="picture secondary img-circle">
        <?= $this->Html->link("", ['controller' => 'artists', 'action' => 'view', $album->artist->slug], ['escape' => false, 'style' => sprintf("background-image:url(%s)", $album->artist->lastfm->getImageUrl())]); ?>
    </div>
    <div class="picture ">
        <?= $this->Html->link("", ['controller' => 'albums', 'action' => 'view', $album->slug], ['escape' => false, 'style' => sprintf("background-image:url(%s)", $album->getImageUrl())]); ?>
    </div>

    <h2><?= $this->Html->link($album->artist->name, ['controller' => 'artists', 'action' => 'view', $album->artist->slug]); ?></h2>
    <h1><?= $this->Html->link($album->name, ['controller' => 'albums', 'action' => 'view', $album->slug]); ?></h1>

    <?php if ($album->hasReleaseDate()) : ?>
        <time datetime="<?= $album->getFormatedReleaseDate("c"); ?>"><?= $album->getFormatedReleaseDate(); ?></time>
    <?php endif; ?>

    <div class="score">
        <?php if($album->snapshot->isNotAvailable()) : ?>
            N/A
        <?php else : ?>
            <?= (int)($album->snapshot->score * 100); ?>%
        <?php endif; ?>
        <span><?= __("Score"); ?></span>
    </div>

    <?php if($album->isNotable()) : ?>
        <div class="notable"><?= __("Notable Album"); ?></div>
    <?php endif; ?>

    <div class="everyone piechart"></div>

</div>

<div class="details">

    <div class="picture secondary img-circle">
        <?= $this->Html->link("", ['controller' => 'artists', 'action' => 'view', $track->album->artist->slug], ['escape' => false, 'style' => sprintf("background-image:url(%s)", $track->album->artist->lastfm->getImageUrl())]); ?>
    </div>
    <div class="picture ">
        <?= $this->Html->link("", ['controller' => 'albums', 'action' => 'view', $track->album->slug], ['escape' => false, 'style' => sprintf("background-image:url(%s)", $track->album->getImageUrl())]); ?>
    </div>

    <h3><?php echo $this->Html->link($track->album->artist->name, ['controller' => 'artists', 'action' => 'view', $track->album->artist->slug]); ?></h3>
    <h2><?php echo $this->Html->link($track->album->name, ['controller' => 'albums', 'action' => 'view', $track->album->slug]); ?></h2>
    <h1><?php echo $this->Html->link($track->title, ['controller' => 'tracks', 'action' => 'view', $track->slug]); ?></h1>

    <?php if ($track->album->hasReleaseDate()) : ?>
        <time datetime="<?= $track->album->getFormatedReleaseDate("c"); ?>"><?= $track->album->getFormatedReleaseDate(); ?></time>
    <?php endif; ?>

    <div class="score">
        <?php if($track->snapshot->isNotAvailable()) : ?>
            N/A
        <?php else : ?>
            <?= (int)($track->snapshot->score * 100); ?>%
        <?php endif; ?>
        <span><?= __("Score"); ?></span>
    </div>

    <div class="everyone piechart"></div>
</div>

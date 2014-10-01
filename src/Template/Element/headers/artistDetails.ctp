 <div class="details">
    <div class="picture img-circle">
        <?= $this->Html->link("", ['controller' => 'artists', 'action' => 'view', $artist->slug], ['escape' => false, 'style' => sprintf("background-image:url(%s)", $artist->lastfm->getImageUrl())]); ?>
    </div>
    <h1><?= $this->Html->link($artist->name, ['controller' => 'artists', 'action' => 'view', $artist->slug]); ?></h1>

    <div class="score">
        <?php if($artist->snapshot->isNotAvailable()) : ?>
            N/A
        <?php else : ?>
            <?= (int)($artist->snapshot->score * 100); ?>%
        <?php endif; ?>
        <span><?= __("Score"); ?></span>
    </div>

    <div class="piechart artist-<?= $artist->id; ?>"></div>
</div>

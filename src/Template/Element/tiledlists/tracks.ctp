 <div class="row tiles tracks">
    <?php foreach($tracks as $track) : ?>
        <div class="col-xs-6 col-md-3 track">
            <?= $this->Html->link(
                    $this->Html->image( $track->album->getImageUrl(), ['alt' => $track->name, 'title' => $track->name]), ['controller' => 'tracks', 'action' => 'view', $track->slug], ['escape' => false, "class" => "thumbnail"] ); ?>
            <h3><?= $this->Html->link($track->title, ['controller' => 'tracks', 'action' => 'view', $track->slug]); ?></h3>

            <?php if(!is_null($track->album)) : ?>
            <div><?= __("on"); ?> <?= $this->Html->link($track->album->name, ['controller' => 'albums', 'action' => 'view', $track->album->slug]); ?></div>
            <?php endif; ?>

            <?php if(!is_null($track->album->artist)) : ?>
            <div><?= __("By"); ?> <?= $this->Html->link($track->album->artist->name, ['controller' => 'artists', 'action' => 'view', $track->album->artist->slug]); ?></div>
            <?php endif; ?>

            <?php if($track->album->hasReleaseDate()) : ?>
                <time datetime="<?= h($track->album->getFormatedReleaseDate("c")); ?>"><?= h($track->album->getFormatedReleaseDate()); ?></time>
            <?php endif; ?>

            <div class="score">
                 <?php if($track->snapshot->isNotAvailable()) : ?>
                    N/A
                <?php else : ?>
                    <?= (int)($track->snapshot->score * 100); ?>%
                <?php endif; ?>
                <span><?= __("Score"); ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

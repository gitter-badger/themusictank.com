 <div class="row tiles albums">
    <?php foreach($albums as $album) : ?>
        <div class="col-xs-6 col-md-3 album">
            <?= $this->Html->link(
                    $this->Html->image( $album->getImageUrl(), ['alt' => $album->name, 'title' => $album->name]), ['controller' => 'albums', 'action' => 'view', $album->slug], ['escape' => false, "class" => "thumbnail"] ); ?>
            <h3><?= $this->Html->link($album->name, ['controller' => 'albums', 'action' => 'view', $album->slug]); ?></h3>

            <?php if(!is_null($album->artist)) : ?>
            <p><?= __("By"); ?> <?= $this->Html->link($album->artist->name, ['controller' => 'artists', 'action' => 'view', $album->artist->slug]); ?></p>
            <?php endif; ?>

            <?php if($album->hasReleaseDate()) : ?>
                <time datetime="<?= h($album->getFormatedReleaseDate("c")); ?>"><?= h($album->getFormatedReleaseDate()); ?></time>
            <?php endif; ?>

            <div class="score">
                 <?php if($album->snapshot->isNotAvailable()) : ?>
                    N/A
                <?php else : ?>
                    <?= (int)($album->snapshot->score * 100); ?>%
                <?php endif; ?>
                <span><?= __("Score"); ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

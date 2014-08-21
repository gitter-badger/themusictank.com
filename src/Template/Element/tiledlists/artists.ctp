<?php
    use Cake\Routing\Router;
?>
<div class="tiled-list artists">
<?php foreach($artists as $idx => $artist) : ?>
    <div class="col-xs-6 col-md-3">
        <h3>
            <a class="thumbnail" href="<?= Router::url(['controller' => 'artists', 'action' => 'view', $artist->slug ]); ?>" style="background-image:url(<?php echo $artist->lastfm->getImageUrl(); ?>);">
                <span><?= h($artist->name); ?></span>
            </a>
        </h3>

        <?php if(count($artist->albums)) : ?>
        <ul class="recent-albums">
            <?php foreach($artist->albums as $i => $album) : if($i >= 3) break;?>
            <li>
                <?php echo $this->Html->link($this->Html->image($album->getImageUrl(), ['alt' => $album->name]), ['controller' => 'albums', 'action' => 'view', $album->slug], ['escape' => false]); ?>
                <?php echo $this->Html->link($album->name, ['controller' => 'albums', 'action' => 'view', $album->slug]); ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>

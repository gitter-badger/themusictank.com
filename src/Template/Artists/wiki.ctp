<?= $this->element('breadcrumbs', ['links' => [
        $this->Html->link(__("Artists"), ['controller' => 'artists', 'action' => 'index']),
        $this->Html->link($artist->name, ['controller' => 'artists', 'action' => 'view',  $artist->slug]),
        $this->Html->link(__("Description"), ['controller' => 'artists', 'action' => 'wiki', $artist->slug])
    ]]);
?>

<div class="header-wrapper">
    <?= $this->Html->image($artist->lastfm->getImageUrl("blur"), ['alt' => $artist->name, 'class' => "blurred"]);  ?>
    <?= $this->Html->image($artist->lastfm->getImageUrl("big"), ['alt' => $artist->name, 'class' => "clean"]);  ?>
    <i class="mask"></i>
</div>

<article class="container container-fluid">

    <header class="collapsed">
        <?php $img = $this->Html->image($artist->lastfm->getImageUrl(), ['alt' => $artist->name, 'class' => "thumbnail"]); ?>
        <?= $this->Html->link($img, ['controller' => 'artists', 'action' => 'view', $artist->slug], ['escape' => false]); ?>

        <h1><?= $this->Html->link($artist->name, ['controller' => 'artists', 'action' => 'view', $artist->slug]); ?></h1>
    </header>

    <div class="row content headerless">

        <div class="col-md-12">
            <h2><?= __("Description"); ?></h2>
            <span class="report-bug" data-bug-type="album wiki" data-location="album/<?= $artist->slug; ?>" data-user="<?= $this->Session->read('Auth.User.User.id'); ?>"><i class="fa fa-bug"></i> <?= __("Wrong/weird bio?"); ?></span>
            <?= $artist->getIntroduction(); ?>
        </div>
        <div class="col-md-12 lastfm"><a href="http://www.last.fm/" target="_blank"><img src="/img/icon-lastfm.png" alt="Last.fm" title="Last.fm" /></a></div>
    </div>

</article>

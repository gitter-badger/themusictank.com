<?= $this->element('breadcrumbs', ['links' => [
        $this->Html->link(__("Artists"), ['controller' => 'artists', 'action' => 'index']),
        $this->Html->link($album->artist->name, ['controller' => 'artists', 'action' => 'view',  $album->artist->slug]),
        $this->Html->link($album->name, ['controller' => 'albums', 'action' => 'view', $album->slug]),
        $this->Html->link(__("Description"), ['controller' => 'albums', 'action' => 'wiki', $album->slug])
    ]]);
?>

<?= $this->element('headers/backdrop', ['entity' => $album]); ?>

<article class="container container-fluid">

    <header>
        <?= $this->element('headers/albumDetails'); ?>
    </header>

    <div class="row content headerless">
        <div class="col-md-12">
            <h2><?=__("Description"); ?></h2>
            <span class="report-bug" data-bug-type="album wiki" data-location="album/<?= $album->slug; ?>" data-user="<?= $this->Session->read('Auth.User.User.id'); ?>"><i class="fa fa-bug"></i> <?= __("Wrong/weird bio?"); ?></span>
            <?= $album->getIntroduction(); ?>
        </div>
        <div class="col-md-12 lastfm"><a href="http://www.last.fm/" target="_blank"><img src="/img/icon-lastfm.png" alt="Last.fm" title="Last.fm" /></a></div>
    </div>

</article>

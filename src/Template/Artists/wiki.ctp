<?= $this->element('breadcrumbs', ['links' => [
        $this->Html->link(__("Artists"), ['controller' => 'artists', 'action' => 'index']),
        $this->Html->link($artist->name, ['controller' => 'artists', 'action' => 'view',  $artist->slug]),
        $this->Html->link(__("Description"), ['controller' => 'artists', 'action' => 'wiki', $artist->slug])
    ]]);
?>

<?= $this->element('headers/backdrop', ['entity' => $artist]); ?>

<article class="container container-fluid">
    <header>
        <?= $this->element('headers/artistDetails'); ?>
    </header>
    <div class="row content headerless">
        <div class="col-md-12">
            <h2><?= __("Description"); ?></h2>
            <span class="report-bug" data-bug-type="album wiki" data-location="album/<?= $artist->slug; ?>" data-user="<?= $this->request->session()->read('Auth.User.User.id'); ?>"><i class="fa fa-bug"></i> <?= __("Wrong/weird bio?"); ?></span>
            <?= $artist->get('introduction'); ?>
        </div>
        <div class="col-md-12 lastfm"><a href="http://www.last.fm/" target="_blank"><img src="/img/icon-lastfm.png" alt="Last.fm" title="Last.fm" /></a></div>
    </div>
</article>

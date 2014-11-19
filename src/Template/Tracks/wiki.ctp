<?= $this->element('breadcrumbs', ['links' => [
        $this->Html->link(__("Artists"), ['controller' => 'artists', 'action' => 'index']),
        $this->Html->link($track->album->artist->name, ['controller' => 'artists', 'action' => 'view',  $track->album->artist->slug]),
        $this->Html->link($track->album->name, ['controller' => 'albums', 'action' => 'view', $track->album->slug]),
        $this->Html->link($track->title, ['controller' => 'tracks', 'action' => 'view', $track->slug]),
        $this->Html->link(__("Description"), ['controller' => 'tracks', 'action' => 'wiki', $track->slug])
    ]]);
?>

<div class="header-wrapper">
    <?= $this->Html->image( $track->album->getImageUrl("blur"), ["alt" => $track->album->name, "class" => "blurred"]);  ?>
    <?= $this->Html->image( $track->album->getImageUrl("big"), ["alt" => $track->album->name, "class" => "clean"]);  ?>
    <i class="mask"></i>
</div>

<article class="container container-fluid">

    <header>
        <?= $this->element('headers/trackDetails'); ?>
    </header>

    <div class="row content headerless">
        <div class="col-md-12">
            <h2><?= __("Description"); ?></h2>
            <span class="report-bug" data-bug-type="track wiki" data-location="tracks/<?= $track->slug; ?>" data-user="<?= $this->request->session()->read('Auth.User.User.id'); ?>"><i class="fa fa-bug"></i> <?= __("Wrong/weird bio?"); ?></span>
            <?= $track->getIntroduction(); ?>
        </div>
        <div class="col-md-12 lastfm"><a href="http://www.last.fm/" target="_blank"><img src="/img/icon-lastfm.png" alt="Last.fm" title="Last.fm" /></a></div>
    </div>

</article>

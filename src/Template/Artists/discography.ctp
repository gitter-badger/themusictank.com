<?php
    $isLogged = $this->Session->check('Auth.User.User.id');
?>
<?= $this->element('breadcrumbs', ['links' => [
        $this->Html->link(__("Artists"), ['controller' => 'artists', 'action' => 'index']),
        $this->Html->link($artist->name, ['controller' => 'artists', 'action' => 'view',  $artist->slug]),
        $this->Html->link(__("Discography"), ['controller' => 'artists', 'action' => 'discography', $artist->slug])
    ]]);
?>

<?= $this->element('headers/backdrop', ['entity' => $artist]); ?>

<article class="container container-fluid">

    <header>
        <?= $this->element('headers/artistDetails'); ?>
    </header>

    <div class="row content headerless">
        <div class="col-md-12">
            <h2><?= __("Discography"); ?></h2>
            <?php if(count($artist->albums) > 0) : ?>
                <?= $this->element('tiledlists/albums', ['albums' => $artist->albums]); ?>
            <?php else : ?>
                <div class="loading-wrap" data-post-load="/ajax/getdiscography/<?php echo $artist->slug; ?>/">
                    <i class="fa fa-refresh fa-spin fa-fw"></i>
                </div>
            <?php endif; ?>
        </div>
    </div>
</article>

<section class="credits">
    <div class="container container-fluid">
        <p>
            <?= __("Artist biography and profile image courtesy of"); ?> <?=$this->Html->link("Last.fm", "http://www.last.fm/", ["target" => "_blank"]); ?>.
            <?php if($artist->lastfm->hasSyncDate()) : ?>
                <?= __("They were last updated on"); ?> <?= $artist->lastfm->getFormattedSyncDate(); ?>.
            <?php endif; ?>
            <?= __("User-contributed text is available under the Creative Commons By-SA License and may also be available under the GNU FDL."); ?>
        </p>
    </div>
</section>

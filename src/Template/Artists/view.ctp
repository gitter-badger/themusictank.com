<?php
    $isLogged = $this->Session->check('Auth.User.User.id');
?>
<?= $this->element('breadcrumbs', ['links' => [
        $this->Html->link(__("Artists"), array('controller' => 'artists', 'action' => 'index')),
        $this->Html->link($artist->name, array('controller' => 'artists', 'action' => 'view',  $artist->slug))
    ]]);
?>

<?= $this->element('headers/backdrop', ['entity' => $artist]); ?>

<article class="container container-fluid">

    <header>

        <?= $this->element('headers/artistDetails'); ?>

        <div class="row stats">

            <div class="col-md-6 enjoyment">
                <div class="col-md-4 enjoyment">
                    <span><?= __("Enjoyed"); ?></span>
                    <em>
                        <?php if(is_null($artist->snapshot)) : ?>
                            N/A
                        <?php else : ?>
                            <?= (int)$artist->snapshot->liking_pct; ?>%
                        <?php endif; ?>
                    </em>
                </div>
                <div class="col-md-4 dislike">
                    <span><?= __("Disliked"); ?></span>
                    <em>
                        <?php if(is_null($artist->snapshot)) : ?>
                            N/A
                        <?php else : ?>
                            <?= (int)$artist->snapshot->disliking_pct; ?>%
                        <?php endif; ?>
                    </em>
                </div>

                <div class="col-md-4 achievements">
                    <span><?= __("Achievements given"); ?></span>
                    <em>
                        N/A
                    </em>
                </div>
            </div>
            <div class="col-md-6 enjoyment">
                <div class="col-md-6 best-track">
                    <span><?=__("Best track"); ?></span>
                    <?php $bestTrack  = $artist->getBestTrack(); ?>
                    <?php if(isset($bestTrack)) : ?>
                        <em><?= $this->Html->link($bestTrack->title, array('controller' => 'tracks', 'action' => 'view', $bestTrack->slug)); ?>&nbsp;<?= (int)$bestTrack["TrackReviewSnapshot"]["liking_pct"]; ?>%</em>
                    <?php else : ?>
                            N/A
                    <?php endif; ?>
                </div>
                <div class="col-md-36worst-track">
                    <span><?= __("Worst track"); ?></span>
                    <?php $worstTrack  = $artist->getWorstTrack(); ?>
                    <?php if(isset($worstTrack)) : ?>
                        <em><?= $this->Html->link($worstTrack->title, array('controller' => 'tracks', 'action' => 'view', $worstTrack->slug)); ?>&nbsp;<?= (int)$worstTrack["TrackReviewSnapshot"]["liking_pct"]; ?>%</em>
                    <?php else : ?>
                            N/A
                    <?php endif; ?>
                </div>

                <div class="col-md-6 best-album">
                    <span><?= __("Best Album"); ?></span>
                    <?php $bestAlbum  = $artist->getBestAlbum(); ?>
                    <?php if(isset($bestAlbum)) : ?>
                        <em><?= $this->Html->link($bestAlbum->name, array('controller' => 'albums', 'action' => 'view', $bestAlbum->slug)); ?>&nbsp;<?= is_null($bestAlbum->snapshot) ? 0 : (int)$bestAlbum->snapshot->liking_pct; ?>%</em>
                    <?php else : ?>
                        N/A
                    <?php endif; ?>
                </div>

                <div class="col-md-6 worst-album">
                    <span><?= __("Worst Album"); ?></span>
                    <?php $worstAlbum  = $artist->getWorstAlbum(); ?>
                    <?php if(isset($worstAlbum)) : ?>
                        <em><?= $this->Html->link($worstAlbum->name, array('controller' => 'albums', 'action' => 'view', $worstAlbum->slug)); ?>&nbsp;<?= is_null($bestAlbum->snapshot) ? 0 : (int)$worstAlbum->snapshot->liking_pct; ?>%</em>
                    <?php else : ?>
                        N/A
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-12 social">
                <?php if($isLogged) : ?>
                    <div class="col-md-3">
                        <h3><?= __("Your subscriptions"); ?></h3>
                        <?php if(is_null($artist->subsciptionsSnapshot)) : ?>
                            N/A
                        <?php else : ?>
                            <?= (int)($artist->subsciptionsSnapshot->score * 100); ?>%
                        <?php endif; ?>
                        <span><?= __("Score"); ?></span>
                    </div>
                    <div class="col-md-3">
                        <div class="piechart subscriptions"></div>
                    </div>

                    <div class="col-md-3">
                        <h3><?= __("You"); ?></h3>
                        <?php if(is_null($artist->userSnapshot)) : ?>
                            N/A
                        <?php else : ?>
                            <?= (int)($artist->userSnapshot->score * 100); ?>%
                        <?php endif; ?>
                        <span><?= __("Score"); ?></span>
                    </div>
                    <div class="col-md-3">
                        <div class="piechart you"></div>
                    </div>
                <?php else : ?>
                    <?php $login = $this->Html->link(__("Login"), ['controller' => 'users', 'action' => 'login', '?' => ["rurl" => '/artist/view/' . $artist->slug]], ['class' => "btn btn-primary"]); ?>
                    <p><?= sprintf(__("%s to see how you and your friends have rated  \"%s\"."), $login, $artist->name); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <?php $wiki =  $artist->get('introduction'); ?>
    <?php if(strlen(strip_tags($wiki)) > 0) : ?>
    <div class="row wiki <?= strlen($wiki) <= 800 ? "full" : ""; ?>">
        <div class="col-md-12 lead">
            <?= substr($wiki, 0, 800); ?></p>
            <i class="mask"></i>
        </div>
        <div class="col-md-4 lastfm"><a href="http://www.last.fm/"><img src="/img/icon-lastfm.png" alt="Last.fm" title="Last.fm" /></a></div>
        <div class="col-md-4 bug"><span class="report-bug" data-bug-type="artist wiki" data-location="artist/<?= $artist->slug; ?>" data-user="<?= $this->Session->read('Auth.User.User.id'); ?>"><i class="fa fa-bug"></i> <?= __("Wrong/weird bio?"); ?></span></div>
        <div class="col-md-4 readmore">
            <?= $this->Html->link(__("Read more"), ['controller' => 'artists', 'action' => 'wiki', $artist->slug], ['class' => "btn btn-primary"]); ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="row content">

        <h2><?= __("Discography"); ?></h2>
        <?php if (count($artist->albums)) : ?>
            <?= $this->element('tiledlists/albums', ['albums' => array_slice($artist->albums, 0, 4)]); ?>
            <?php if (count($artist->albums) > 4) : ?>
                <p><?= $this->Html->link(__("View more"), ['controller' => 'artists', 'action' => 'discography', $artist->slug], ['class' => "btn btn-primary pull-right"]); ?></p>
            <?php endif; ?>
        <?php else : ?>
            <div class="loading-wrap" data-post-load="/ajax/getdiscography/<?= $artist->slug; ?>/">
                <i class="fa fa-refresh fa-spin fa-fw"></i>
            </div>
        <?php endif; ?>
    </div>
</article>
<section class="credits">
    <div class="container container-fluid">
        <p>
            <?= __("Artist biography and profile image courtesy of"); ?> <?=$this->Html->link("Last.fm", "http://www.last.fm/", ["target" => "_blank"]); ?>.
            <?php if($artist->hasSyncDate()) : ?>
                <?= __("They were last updated"); ?> <?= $artist->getFormattedSyncDate(); ?>.
            <?php endif; ?>
            <?= __("User-contributed text is available under the Creative Commons By-SA License and may also be available under the GNU FDL."); ?>
        </p>
    </div>
</section>

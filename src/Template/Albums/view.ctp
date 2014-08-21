<?php
    $isLogged = $this->Session->check('Auth.User.User.id');
?>

<?= $this->element('breadcrumbs', ['links' => [
        $this->Html->link(__("Artists"), ['controller' => 'artists', 'action' => 'index']),
        $this->Html->link($album->artist->name, ['controller' => 'artists', 'action' => 'view',  $album->artist->slug]),
        $this->Html->link($album->name, ['controller' => 'albums', 'action' => 'view', $album->slug])
    ]]);
?>

<div class="header-wrapper">
    <?= $this->Html->image( $album->getImageUrl("blur"), ["alt" => $album->name, "class" => "blurred"]);  ?>
    <?= $this->Html->image( $album->getImageUrl("big"), ["alt" => $album->name, "class" => "clean"]);  ?>
    <i class="mask"></i>
</div>

<article class="container container-fluid">

    <header>

        <?php $img = $this->Html->image( $album->getImageUrl(), ["alt" => $album->name, "class" => "thumbnail"]); ?>
        <?= $this->Html->link($img, ['controller' => 'albums', 'action' => 'view', $album->slug], ["escape" => false]); ?>

        <h1><?= $this->Html->link($album->name, ['controller' => 'albums', 'action' => 'view', $album->slug]); ?></h1>

        <h2><?= $this->Html->link($album->artist->name, ['controller' => 'artists', 'action' => 'view', $album->artist->slug]); ?></h2>

        <?php if ($album->hasReleaseDate()) : ?>
            <time datetime="<?= $album->getFormatedReleaseDate("c"); ?>"><?= $album->getFormatedReleaseDate(); ?></time>
        <?php endif; ?>

        <div class="score">
            <?php if($album->snapshot->isNotAvailable()) : ?>
                N/A
            <?php else : ?>
                <?= (int)($album->snapshot->score * 100); ?>%
            <?php endif; ?>
            <span><?= __("Score"); ?></span>
        </div>

        <?php if($album->isNotable()) : ?>
            <div class="notable"><?= __("Notable Album"); ?></div>
        <?php endif; ?>

        <div class="everyone piechart"></div>

        <div class="row stats">

            <div class="col-md-2 enjoyment">
                <span><?= __("Enjoyed"); ?></span>
                <em>
                    <?php if($album->snapshot->isNotAvailable()) : ?>
                        N/A
                    <?php else : ?>
                        <?= (int)$album->snapshot->liking_pct; ?>%
                    <?php endif; ?>
                </em>
            </div>
            <div class="col-md-2 dislike">
                <span><?= __("Disliked"); ?></span>
                <em>
                    <?php if($album->snapshot->isNotAvailable()) : ?>
                        N/A
                    <?php else : ?>
                        <?= (int)$album->snapshot->disliking_pct; ?>%
                    <?php endif; ?>
                </em>
            </div>

            <?php $bestTrack  = $album->getBestTrack(); ?>
            <?php if(isset($bestTrack)) : ?>
            <div class="col-md-4 best-track">
                <span><?=__("Best track"); ?></span>
                <em><?= $this->Html->link($bestTrack->title, array('controller' => 'tracks', 'action' => 'view', $bestTrack->slug)); ?>&nbsp;<?= (int)$bestTrack["TrackReviewSnapshot"]["liking_pct"]; ?>%</em>
            </div>
            <?php endif; ?>

            <?php $worstTrack  = $album->getWorstTrack(); ?>
            <?php if(isset($worstTrack)) : ?>
            <div class="col-md-4 worst-track">
                <span><?= __("Worst track"); ?></span>
                <em><?= $this->Html->link($worstTrack->title, array('controller' => 'tracks', 'action' => 'view', $worstTrack->slug)); ?>&nbsp;<?= (int)$worstTrack["TrackReviewSnapshot"]["liking_pct"]; ?>%</em>
            </div>
            <?php endif; ?>

            <div class="col-md-12 social">
                <?php if($isLogged) : ?>
                    <div class="col-md-3">
                        <h3><?= __("Your subscriptions"); ?></h3>
                        <?php if($album->subsciptionsSnapshot->isNotAvailable()) : ?>
                            N/A
                        <?php else : ?>
                            <?= (int)($album->subsciptionsSnapshot->score * 100); ?>%
                        <?php endif; ?>
                        <span><?= __("Score"); ?></span>
                    </div>
                    <div class="col-md-3">
                        <div class="piechart subscriptions"></div>
                    </div>

                    <div class="col-md-3">
                        <h3><?= __("You"); ?></h3>
                        <?php if($album->userSnapshot->isNotAvailable()) : ?>
                            N/A
                        <?php else : ?>
                            <?= (int)($album->userSnapshot->score * 100); ?>%
                        <?php endif; ?>
                        <span><?= __("Score"); ?></span>
                    </div>
                    <div class="col-md-3">
                        <div class="piechart you"></div>
                    </div>
                <?php else : ?>
                    <?php $login = $this->Html->link(__("Login"), ['controller' => 'users', 'action' => 'login', '?' => ["rurl" => '/albums/view/' . $album->slug]], ["class" => "btn btn-primary"]); ?>
                    <p><?= sprintf(__("%s to see how you and your friends have rated  \"%s\"."), $login, $album->name); ?></p>
                <?php endif; ?>
            </div>

        </div>
    </header>

    <?php $wiki =  $album->getIntroduction(); ?>
    <?php if(strlen($wiki) > 0) : ?>
    <div class="row wiki <?= strlen($wiki) <= 800 ? "full" : ""; ?>">
        <div class="col-md-12 lead">
            <?= substr($wiki, 0, 800); ?></p>
            <i class="mask"></i>
        </div>
        <div class="col-md-4 lastfm"><a href="http://www.last.fm/"><img src="/img/icon-lastfm.png" alt="Last.fm" title="Last.fm" /></a></div>
        <div class="col-md-4 bug"><span class="report-bug" data-bug-type="album wiki" data-location="album/<?= $album->slug; ?>" data-user="<?= $this->Session->read('Auth.User.User.id'); ?>"><i class="fa fa-bug"></i> <?= __("Wrong/weird bio?"); ?></span></div>
        <div class="col-md-4 readmore">
            <?php if(strlen($wiki) > 800) : ?>
                <?= $this->Html->link(__("Read more"), ['controller' => 'albums', 'action' => 'wiki', $album->slug], ["class" => "btn btn-primary"]); ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="row content">

        <?php if(count($album->tracks)) : ?>
            <h2><?= __("Overview"); ?></h2>
            <div class="row">
                <div class="col-md-3">
                    <ul class="tracklisting">
                    <?php foreach ($album->tracks as $idx => $track) : ?>
                        <li>
                            <?= $this->Html->link($track->title, ['controller' => 'tracks', 'action' => 'view', $track->slug]); ?>
                            <div class="piechart track-<?= $idx; ?>"></div>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-md-9 big-graph"></div>
            </div>

            <h2><?= __("Recent Reviewers"); ?></h2>
            <div class="col-md-6">
                <?php $recentReviewers = $album->getRecentReviewers(); ?>
                <?php if(count($recentReviewers)) : ?>
                    <ul>
                        <?php foreach($recentReviewers as $reviewer) : ?>
                        <li><?= $this->Html->link($this->Html->image($reviewer->getImageUrl(), ["alt" => $reviewer->username]), ['controller' => 'tracks', 'action' => 'by_user', $album->slug, $reviewer->slug], ['escape' => false]);  ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php elseif(count($album->tracks)) : ?>
                    <?php $login = $this->Html->link(__("Review"), ['controller' => 'tracks', 'action' => 'review', $album->tracks[0]->slug], ["class" => "btn btn-primary"]); ?>
                    <p><?php echo sprintf(__("Be the first to %s a track off \"%s\"."), $login, $album->name); ?></p>
                <?php endif; ?>
            </div>

            <div class="col-md-6">

            <?php if ($isLogged) : ?>
                <?php $subscriptions = $album->getRecentSubscriptionsReviewers($this->Session->get('Auth.User.User.id')); ?>
                <?php if(count($subscriptions) > 0) : ?>
                    <ul>
                        <?php foreach($subscriptions as $idx => $subscription) : ?>
                        <li>
                            <?= $this->Html->link($this->Html->image($subscription->getImageUrl(), ["alt" => $subscription->username]), ['controller' => 'albums', 'action' => 'by_user', $album->slug, $subscription->slug], ['escape' => false]);  ?>
                        </li>
                        <?php if($idx >= 3 && (count($subscriptions) - 4 > 0)) :  ?>
                            <li class="others"><?= sprintf(__("+ %s others"), count($subscriptions) - 4); ?></li>
                        <?php break; endif; ?>
                        <?php endforeach; ?>
                    </ul>
                    <p>
                        <?= $this->Html->link(sprintf(__("%s of the people you are subscribed to reviewed %s."), count($subscriptions), $track->title), ['controller' => 'tracks', 'action' => 'by_subscriptions', $track->slug]); ?>
                    </p>
                <?php else : ?>
                    <p><?= __("None of your subscriptions have reviewed a track on this album."); ?></p>
                <?php endif; ?>
            <?php else : ?>
                <?php $login = $this->Html->link(__("Login"), ['controller' => 'users', 'action' => 'login', '?' => ["rurl" => '/albums/view/' . $album->slug]], ["class" => "btn btn-primary"]); ?>
                <p><?= sprintf(__("%s to see which of your friends have rated  \"%s\"."), $login, $album->name); ?></p>
            <?php endif; ?>

        <?php else : ?>
            <div class="col-md-12">
                <p class="lead"><?= __("We could not fetch the album's tracks from the API for the moment."); ?></p>
                 <div class="loading-wrap">
                    <i class="fa fa-cog fa-spin fa-fw"></i>
                </div>
            </div>
        <?php endif; ?>
        </div>
    </div>
</article>

<section class="credits">
    <div class="container container-fluid">
        <p>
            <?= __("Album description courtesy of"); ?> <?= $this->Html->link("Last.fm", "http://www.last.fm/", ["target" => "_blank"]); ?>.
            <?php if($album->lastfm->hasSyncDate()) : ?>
                <?= __("It was last updated on"); ?> <?= $album->lastfm->getFormattedSyncDate(); ?>.
            <?php endif; ?>
            <?= __("User-contributed text is available under the Creative Commons By-SA License and may also be available under the GNU FDL."); ?>
        </p>
    </div>
</section>


<?php $this->start('bottom-extra'); ?>
<?php if($album->lastfm->hasSyncDate()) : ?>
<script>$(function(){
    var svg = d3.select(".big-graph").append("svg");
    <?php if(isset($album->snapshot)) : ?>
        tmt.createRange(svg, <?php echo json_encode($album->snapshot->ranges); ?>, {key: "everyone range-everyone", total: <?php echo (int)$album->duration; ?>});
        tmt.createLine(svg, <?php echo json_encode($album->snapshot->curve); ?>, {key: "everyone line-everyone", total: <?php echo (int)$album->duration; ?>});
        tmt.createPie(".everyone.piechart", [{"type" : "smile", "value" : <?php echo (int)$album->snapshot->liking_pct; ?>}, {"type" : "meh", "value" : <?php echo (int)$album->snapshot->neutral_pct; ?>}, {"type" : "frown", "value" : <?php echo (int)$album->snapshot->disliking_pct; ?>}], {key: "tanker chart-tanker"});
    <?php endif; ?>
    <?php /* if(isset($album->subscriptionsSnaption)) : ?>
        tmt.createRange(svg, <?php echo json_encode($userAlbumReviewSnapshot["ranges"]); ?>, {key: "user range-user", total: <?php echo (int)$album["duration"]; ?>});
        tmt.createLine(svg, <?php echo json_encode($userAlbumReviewSnapshot["curve"]); ?>, {key: "user line-user", total: <?php echo (int)$album["duration"]; ?>});
        tmt.createPie(".uars.piechart", [{"type" : "smile", "value" : <?php echo (int)$userAlbumReviewSnapshot["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo (int)$userAlbumReviewSnapshot["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo (int)$userAlbumReviewSnapshot["disliking_pct"]; ?>}], {key: "tanker chart-tanker"});
    <?php endif; ?>
    <?php if(isset($profileAlbumReviewSnapshot)) : ?>
        tmt.createRange(svg, <?php echo json_encode($profileAlbumReviewSnapshot["ranges"]); ?>, {key: "profile range-profile", total: <?php echo (int)$album["duration"]; ?>});
        tmt.createLine(svg, <?php echo json_encode($profileAlbumReviewSnapshot["curve"]); ?>, {key: "profile line-profile", total: <?php echo (int)$album["duration"]; ?>});
    <?php endif; */ ?>
});</script>
<?php endif; ?>
<?php $this->end(); ?>

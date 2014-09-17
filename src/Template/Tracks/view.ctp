<?php
    $isLogged = $this->Session->check('Auth.User.User.id');
?>

<?= $this->element('breadcrumbs', ['links' => [
        $this->Html->link(__("Artists"), ['controller' => 'artists', 'action' => 'index']),
        $this->Html->link($track->album->artist->name, ['controller' => 'artists', 'action' => 'view',  $track->album->artist->slug]),
        $this->Html->link($track->album->name, ['controller' => 'albums', 'action' => 'view', $track->album->slug]),
        $this->Html->link($track->title, ['controller' => 'tracks', 'action' => 'view', $track->slug])
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

        <div class="row stats">

            <div class="col-md-2 enjoyment">
                <span><?php echo __("Enjoyed"); ?></span>
                <em>
                    <?php if($track->snapshot->isNotAvailable()) : ?>
                        N/A
                    <?php else : ?>
                        <?= (int)$track->snapshot->liking_pct; ?>%
                    <?php endif; ?>
                </em>
            </div>
            <div class="col-md-2 dislike">
                <span><?php echo __("Disliked"); ?></span>
                <em>
                    <?php if($track->snapshot->isNotAvailable()) : ?>
                        N/A
                    <?php else : ?>
                        <?= (int)$track->snapshot->disliking_pct; ?>%
                    <?php endif; ?>
                </em>
            </div>

            <?php $bestArea = $track->getBestArea(); ?>
            <?php if(isset($bestArea)) : ?>
            <div class="col-md-4 best best-part">
                <span><?php echo __("Best area"); ?></span>
                --
            </div>
            <?php endif; ?>

            <?php $worstArea = $track->getWorstArea(); ?>
            <?php if(isset($worstArea)) : ?>
            <div class="col-md-4 worst worst-area">
                <span><?php echo __("Worst area"); ?></span>
                --
            </div>
            <?php endif; ?>

            <div class="col-md-12 social">
                <?php if($isLogged) : ?>
                    <div class="col-md-3">
                        <h3><?= __("Your subscriptions"); ?></h3>
                        <?php if($track->subsciptionsSnapshot->isNotAvailable()) : ?>
                            N/A
                        <?php else : ?>
                            <?= (int)($track->subsciptionsSnapshot->score * 100); ?>%
                        <?php endif; ?>
                        <span><?= __("Score"); ?></span>
                    </div>
                    <div class="col-md-3">
                        <div class="piechart subscriptions"></div>
                    </div>

                    <div class="col-md-3">
                        <h3><?= __("You"); ?></h3>
                        <?php if($track->userSnapshot->isNotAvailable()) : ?>
                            N/A
                        <?php else : ?>
                            <?= (int)($track->userSnapshot->score * 100); ?>%
                        <?php endif; ?>
                        <span><?= __("Score"); ?></span>
                    </div>
                    <div class="col-md-3">
                        <div class="piechart you"></div>
                    </div>
                <?php else : ?>
                    <?php $login = $this->Html->link(__("Login"), ['controller' => 'users', 'action' => 'login', '?' => ["rurl" => '/tracks/view/' . $track->slug]], ["class" => "btn btn-primary"]); ?>
                    <p><?= sprintf(__("%s to see how you and your friends have rated  \"%s\"."), $login, $track->name); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <?php $wiki =  $track->getIntroduction(); ?>
    <?php if(strlen($wiki) > 0) : ?>
    <div class="row wiki <?= strlen($wiki) <= 800 ? "full" : ""; ?>">
        <div class="col-md-12 lead">
            <?= substr($wiki, 0, 800); ?></p>
            <i class="mask"></i>
        </div>
        <div class="col-md-4 lastfm"><a href="http://www.last.fm/" target="_blank"><img src="/img/icon-lastfm.png" alt="Last.fm" title="Last.fm" /></a></div>
        <div class="col-md-4 bug"><span class="report-bug" data-bug-type="track wiki" data-location="tracks/<?= $track->slug; ?>" data-user="<?= $this->Session->read('Auth.User.User.id'); ?>"><i class="fa fa-bug"></i> <?= __("Wrong/weird bio?"); ?></span></div>
        <div class="col-md-4 readmore">
            <?php if(strlen($wiki) > 800) : ?>
                <?= $this->Html->link(__("Read more"), ['controller' => 'tracks', 'action' => 'wiki', $track->slug], ["class" => "btn btn-primary"]); ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>


    <div class="row content">

        <div class="big-graph"></div>

        <div class="streamer" <?= $track->getPlayerAttributes(); ?>>

            <div class="progress-wrap">
                <div class="progress">
                  <div class="progress-bar loaded-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="<?= $track->duration; ?>" style="width: 0%;"></div>
                  <div class="progress-bar playing-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="<?= $track->duration; ?>" style="width: 0%;"></div>
                </div>
               <div class="cursor"></div>
           </div>

            <div class="position">0</div>
            <i class="play fa fa-stop"></i>
            <div class="duration"></div>

            <small class="report-bug" data-bug-type="track player" data-location="artist: <?= $track->album->artist->slug; ?>, album: <?= $track->album->slug; ?>, track: <?= $track->slug; ?>" data-user="<?= $this->Session->read('Auth.User.User.id'); ?>"><i class="fa fa-bug"></i> <?= __("Wrong song?"); ?></small>
        </div>


            <?php /*if($isLogged) :  ?>
                <?php if(isset($subsTrackReviewSnapshot)) : ?>
                <section class="col-md-12">
                    <?php if(!count($subsTrackReviewSnapshot)) : ?>
                        <p><?php echo sprintf("Your subscribers have not reviewed %s yet.", $track['title']); ?></p>
                    <?php else : ?>
                        <div class="rsABlock strs piechart" data-move-effect="left"></div>
                        <p class="rsABlock" data-move-effect="right"><?php echo $this->StringMaker->composeTrackAppreciation($subsTrackReviewSnapshot, $track, $album, $artist); ?></p>
                        <p dclass="rsABlock" data-move-effect="right"><?php echo $this->StringMaker->composeTimedAppreciation($subsTrackReviewSnapshot, $track["duration"]); ?></p>
                        <div class="rsCaption"><?php echo __("People you are subscribed to"); ?></div>
                    <?php endif; ?>
                </section>
                <?php endif; ?>

                <?php if(isset($userTrackReviewSnapshot)) : ?>
                <section class="col-md-12">
                    <?php if(!count($userTrackReviewSnapshot)) : ?>
                        <p>Review '<?php echo $track["title"]; ?>' you wish to see how your opinion compares with others.</p>
                        <p><?php echo $this->Html->link(__("Review track"), array('controller' => 'player', 'action' => 'play', $track["slug"]), array("class" => "btn btn-primary")); ?></p>
                    <?php else : ?>
                        <div class="rsABlock utrs piechart" data-move-effect="left"></div>
                        <p class="rsABlock" data-move-effect="right"><?php echo $this->StringMaker->composeTrackAppreciation($userTrackReviewSnapshot, $track, $album, $artist); ?></p>
                        <p class="rsABlock" data-move-effect="right"><?php echo $this->StringMaker->composeTimedAppreciation($userTrackReviewSnapshot, $track["duration"]); ?></p>
                        <div class="rsCaption"><?php echo __("You"); ?></div>
                    <?php endif; ?>
                </section>
                <?php endif; ?>
            <?php else : ?>
                <section class="col-md-12">
                    <?php $login = $this->Html->link(__("Login"), array('controller' => 'users', 'action' => 'login', '?' => array("rurl" => '/tracks/view/' . $track['slug']))); ?>
                    <p class="rsABlock" data-move-effect="right"><?php echo sprintf(__("%s to see how you have rated %s"), $login, $track["title"]); ?></p>
                    <div class="rsCaption"><?php echo __("You"); ?></div>
                </section>
            <?php endif; ?>

            <?php if(array_key_exists("top", $trackReviewSnapshot)) : ?>
                <?php if(count($trackReviewSnapshot["top"]) > 1 && count($trackReviewSnapshot["bottom"] > 1)) : ?>
                    <div class="row">
                        <div class="col-md-6 highlight">
                            <p><?php echo $this->StringMaker->composeTrackHighlight($trackReviewSnapshot); ?></p>
                            <div class="highgraph"></div>
                            <button type="button" data-from="<?php echo $trackReviewSnapshot["top"][0]; ?>" data-to="<?php echo $trackReviewSnapshot["top"][1]; ?>">Play</button>
                        </div>
                        <div class="col-md-6 lowlight">
                            <p><?php echo $this->StringMaker->composeTrackLowpoint($trackReviewSnapshot); ?></p>
                            <div class="lowgraph"></div>
                            <button type="button" data-from="<?php echo $trackReviewSnapshot["bottom"][0]; ?>" data-to="<?php echo $trackReviewSnapshot["bottom"][1]; ?>">Play</button>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif;*/ ?>

            <div class="row">
                <div class="col-md-6">
                <h2><?php echo __("Recent Reviewers"); ?></h2>
                    <?php $recentReviewers = $track->getRecentReviewers(); ?>
                    <?php if(count($recentReviewers)) : ?>
                        <ul>
                            <?php foreach($recentReviewers as $reviewer) : ?>
                            <li><?= $this->Html->link($this->Html->image($reviewer->getImageUrl(), ["alt" => $reviewer->username]), ['controller' => 'tracks', 'action' => 'by_user', $track->slug, $reviewer->slug], ['escape' => false]);  ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <?php $login = $this->Html->link(__("Review"), ['controller' => 'tracks', 'action' => 'review', $track->slug], ["class" => "btn btn-primary"]); ?>
                        <p><?php echo sprintf(__("Be the first to %s \"%s\"."), $login, $album->name); ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <?php if ($isLogged) : ?>
                        <?php $subscriptions = $track->getRecentSubscriptionsReviewers($this->Session->get('Auth.User.User.id')); ?>
                        <?php if(count($subscriptions) > 0) : ?>
                            <ul>
                                <?php foreach($subscriptions as $idx => $subscription) : ?>
                                <li>
                                    <?= $this->Html->link($this->Html->image($subscription->getImageUrl(), ["alt" => $subscription->username]), ['controller' => 'tracks', 'action' => 'by_user', $track->slug, $subscription->slug], ['escape' => false]);  ?>
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
                        <?php $login = $this->Html->link(__("Login"), ['controller' => 'users', 'action' => 'login', '?' => ["rurl" => '/track/view/' . $track->slug]], ["class" => "btn btn-primary"]); ?>
                        <p><?= sprintf(__("%s to see which of your friends have rated  \"%s\"."), $login, $track->title); ?></p>
                    <?php endif; ?>

                </div>
            </div>
        </div>

    </div>

</article>

<section class="credits">
    <div class="container container-fluid">
        <p>
            <?php echo __("Track description courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", ["target" => "_blank"]); ?>.
            <?php if($track->lastfm->hasSyncDate()) : ?>
                <?= __("They were last updated on"); ?> <?= $track->lastfm->getFormattedSyncDate(); ?>.
            <?php endif; ?>
            <?php echo __("User-contributed text is available under the Creative Commons By-SA License and may also be available under the GNU FDL."); ?>
        </p>
    </div>
</section>



<?php $this->start('bottom-extra'); ?>
<script>$(function(){
var svg = d3.select(".big-graph").append("svg");

<?php if(!is_null($track->youtube)) : ?>
tmt.waveform(svg, <?php echo json_encode($track->youtube->waveform); ?>, {key: "waveform", total: <?php echo (int)$track->duration; ?>});
<?php endif; ?>

<?php if(!is_null($track->snapshot)) : ?>
tmt.createRange(svg, <?php echo json_encode($track->snapshot->ranges); ?>, {key: "everyone range-everyone", total: <?php echo (int)$track->duration; ?>});
tmt.createLine(svg, <?php echo json_encode($track->snapshot->curve); ?>, {key: "everyone line-everyone", total: <?php echo (int)$track->duration; ?>});
tmt.createPie(".everyone.piechart", [{"type" : "smile", "value" : <?php echo $track->snapshot->liking_pct; ?>}, {"type" : "meh", "value" : <?php echo $track->snapshot->neutral_pct; ?>}, {"type" : "frown", "value" : <?php echo $track->snapshot->disliking_pct; ?>}], {key: "tanker chart-tanker"});
<?php endif; ?>

<?php if (!is_null($track->snapshot->top) && count($track->snapshot->top > 1)) : ?>
var highgraph = d3.select(".highgraph").append("svg");
tmt.createRange(highgraph, <?php echo json_encode(array_slice($track->snapshot->ranges, $track->snapshot->top[0], $track->snapshot->top[1])); ?>, {key: "everyone range-everyone", total: 30});
tmt.createLine(highgraph, <?php echo json_encode(array_slice($track->snapshot->curve, $track->snapshot->top[0], $track->snapshot->top[1])); ?>, {key: "everyone line-everyone", total: 30});
<?php endif; ?>

<?php if (!is_null($track->snapshot->bottom) && count($track->snapshot->bottom > 1)) : ?>
var highgraph = d3.select(".lowgraph").append("svg");
tmt.createRange(highgraph, <?php echo json_encode(array_slice($track->snapshot->ranges, $track->snapshot->bottom[0], $track->snapshot->bottom[1])); ?>, {key: "everyone range-everyone", total: 30});
tmt.createLine(highgraph, <?php echo json_encode(array_slice($track->snapshot->curve, $track->snapshot->bottom[0], $track->snapshot->bottom[1])); ?>, {key: "everyone line-everyone", total: 30});
<?php endif; ?>

<?php if(!is_null($track->userSnapshot)) : ?>
tmt.createRange(svg, <?php echo json_encode($track->userSnapshot->ranges); ?>, {key: "everyone range-everyone", total: <?php echo (int)$track->duration; ?>});
tmt.createLine(svg, <?php echo json_encode($track->userSnapshot->curve); ?>, {key: "everyone line-everyone", total: <?php echo (int)$track->duration; ?>});
tmt.createPie(".everyone.piechart", [{"type" : "smile", "value" : <?php echo $track->userSnapshot->liking_pct; ?>}, {"type" : "meh", "value" : <?php echo $track->userSnapshot->neutral_pct; ?>}, {"type" : "frown", "value" : <?php echo $track->userSnapshot->disliking_pct; ?>}], {key: "tanker chart-tanker"});
<?php endif; ?>

<?php if(!is_null($track->subscriberSnapshot)) : ?>
tmt.createRange(svg, <?php echo json_encode($track->subscriberSnapshot->ranges); ?>, {key: "everyone range-everyone", total: <?php echo (int)$track->duration; ?>});
tmt.createLine(svg, <?php echo json_encode($track->subscriberSnapshot->curve); ?>, {key: "everyone line-everyone", total: <?php echo (int)$track->duration; ?>});
tmt.createPie(".everyone.piechart", [{"type" : "smile", "value" : <?php echo $track->subscriberSnapshot->liking_pct; ?>}, {"type" : "meh", "value" : <?php echo $track->subscriberSnapshot->neutral_pct; ?>}, {"type" : "frown", "value" : <?php echo $track->subscriberSnapshot->disliking_pct; ?>}], {key: "tanker chart-tanker"});
<?php endif; ?>


<?php if(isset($profileTrackReviewSnapshot)) : ?>
tmt.createRange(svg, <?php echo json_encode($profileTrackReviewSnapshot["ranges"]); ?>, {key: "profile range-profile", total: <?php echo (int)$track["duration"]; ?>});
tmt.createLine(svg, <?php echo json_encode($profileTrackReviewSnapshot["curve"]); ?>, {key: "profile line-profile", total: <?php echo (int)$track["duration"]; ?>});
<?php endif; ?>

<?php if(isset($previousTrack)) : ?>
tmt.createPie(".prevtrack.piechart", [{"type" : "smile", "value" : <?php echo (int)$previousTrack["TrackReviewSnapshot"]["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo (int)$previousTrack["TrackReviewSnapshot"]["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo (int)$previousTrack["TrackReviewSnapshot"]["disliking_pct"]; ?>}], {key: "prev chart-tanker"});
<?php endif; ?>

<?php if(isset($nextTrack)) : ?>
tmt.createPie(".nexttrack.piechart", [{"type" : "smile", "value" : <?php echo (int)$nextTrack["TrackReviewSnapshot"]["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo (int)$nextTrack["TrackReviewSnapshot"]["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo (int)$nextTrack["TrackReviewSnapshot"]["disliking_pct"]; ?>}], {key: "next chart-tanker"});
<?php endif; ?>
});</script>
<?php $this->end(); ?>

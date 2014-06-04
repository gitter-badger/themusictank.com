<div class="container container-fluid">

    <nav class="sub-menu">
        <ol class="breadcrumb">
            <li><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></li>
            <li><?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?></li>
            <li class="active">"<?php echo $this->Html->link($track["title"], array('controller' => 'tracks', 'action' => 'view', $track["slug"])); ?>"</li>
        </ol>
    </nav>

    <article class="heading track-profile">
        <div class="thumbnail">
            <?php echo $this->Html->image( $this->App->getImageUrl($album, true), array("alt" => $album["name"])); ?>
        </div>

        <aside>
            <h1><?php echo $track["title"]; ?></h1>

            <section class="description expandable">
                <div class="wrapper">
                    <?php if(empty($lastfmTrack["wiki"])) : ?>
                        <p><?php echo sprintf(__("This is track number %s off of %s."), $track["track_num"], $album["name"]); ?></p>
                    <?php else : ?>
                         <?php echo $lastfmTrack["wiki"]; ?>
                    <?php endif; ?>
                </div>
            </section>

            <div class="track-context">
                <?php if(isset($previousTrack)) : ?>
                <ol start="<?php echo $previousTrack["track_num"]; ?>">
                <?php else : ?>
                    <ol class="<?php echo isset($nextTrack) ? "begin" : "end" ?>" start="<?php echo $track["track_num"]; ?>">
                <?php endif; ?>
                    <?php if(isset($previousTrack)) : ?>
                    <li>
                        <?php echo $previousTrack["title"]; ?>
                    </li>
                    <?php endif; ?>
                    <li><?php echo $track["title"]; ?></li>
                    <li>
                    <?php if(isset($nextTrack)) : ?>
                        <?php echo $nextTrack["title"]; ?>
                    <?php endif; ?>
                    </li>
                </ol>
            </div>

            <ul class="pager">
                <?php if(isset($previousTrack)) : ?>
                    <li class="previous"><?php echo $this->Html->link("&larr; " . $previousTrack["title"], array('controller' => 'tracks', 'action' => 'view', $previousTrack["slug"]), array('escape' => false)); ?></li>
                <?php endif; ?>
                <?php if(isset($nextTrack)) : ?>
                    <li class="next"><?php echo $this->Html->link($nextTrack["title"] . " &rarr;", array('controller' => 'tracks', 'action' => 'view', $nextTrack["slug"]), array('escape' => false)); ?></li>
                <?php endif; ?>
            </ul>
        </aside>

        <div class="statistics">
            <section class="tankers">
                <?php echo $this->Chart->getBigPie("track", $artist["slug"], $trackReviewSnapshot); ?>
                <h3><?php echo __("General"); ?></h3>
                <ul>
                    <li class="average"><?php echo $this->Chart->formatScore($trackReviewSnapshot["score_snapshot"]); ?></li>
                    <li class="enjoyment"><?php echo $this->Chart->formatPct($trackReviewSnapshot["liking_pct"]); ?><i class="fa fa-smile-o"></i></li>
                    <li class="neutral"><?php echo $this->Chart->formatPct($trackReviewSnapshot["neutral_pct"]); ?><i class="fa fa-meh-o"></i></li>
                    <li class="displeasure"><?php echo $this->Chart->formatPct($trackReviewSnapshot["disliking_pct"]); ?><i class="fa fa-frown-o"></i></li>
                </ul>
            </section>

            <?php if(isset($userArtistReviewSnapshot)) : ?>
                <section class="subscribers">
                    <h3><?php echo __("Subscriptions"); ?></h3>
                    <?php echo $this->Chart->getBigPie("track", $artist["slug"], $userArtistReviewSnapshot); ?>
                    <ul>
                        <li class="average"><?php echo $this->Chart->formatScore($userArtistReviewSnapshot["score_snapshot"]); ?></li>
                        <li class="enjoyment"><?php echo $this->Chart->formatPct($userArtistReviewSnapshot["liking_pct"]); ?><i class="fa fa-smile-o"></i></li>
                        <li class="neutral"><?php echo $this->Chart->formatPct($userArtistReviewSnapshot["neutral_pct"]); ?><i class="fa fa-meh-o"></i></li>
                        <li class="displeasure"><?php echo $this->Chart->formatPct($userArtistReviewSnapshot["disliking_pct"]); ?><i class="fa fa-frown-o"></i></li>
                    </ul>
                </section>
            <?php endif; ?>


            <?php if(isset($subsAlbumReviewSnapshot)) : ?>
            <section class="subscribers">
                <?php if(count($subsTrackReviewSnapshot) > 0) : ?>
                    <h3><?php echo __("Subscriptions"); ?></h3>
                    <?php echo $this->Chart->getBigPie("track", $track["slug"], $subsTrackReviewSnapshot); ?>
                    <ul>
                        <li class="average"><?php echo $this->Chart->formatScore($subsTrackReviewSnapshot["score_snapshot"]); ?></li>
                        <li class="enjoyment"><?php echo $this->Chart->formatPct($subsTrackReviewSnapshot["liking_pct"]); ?><i class="fa fa-smile-o"></i></li>
                        <li class="neutral"><?php echo $this->Chart->formatPct($subsTrackReviewSnapshot["neutral_pct"]); ?><i class="fa fa-meh-o"></i></li>
                        <li class="displeasure"><?php echo $this->Chart->formatPct($subsTrackReviewSnapshot["disliking_pct"]); ?><i class="fa fa-frown-o"></i></li>
                    </ul>
                <?php else : ?>
                    <p><?php echo __("None of the people you are subscribed to have reviewed this track yet."); ?></p>
                <?php endif; ?>
            </section>
            <?php endif; ?>

            <?php if(isset($userAlbumReviewSnapshot)) : ?>
            <section class="you">
                <?php if(count($userTrackReviewSnapshot) > 0) : ?>
                    <h3><?php echo __("Your rating"); ?></h3>
                    <?php echo $this->Chart->getBigPie("track", $track["slug"], $userTrackReviewSnapshot); ?>
                    <ul>
                        <li class="average"><?php echo $this->Chart->formatScore($userTrackReviewSnapshot["score_snapshot"]); ?></li>
                        <li class="enjoyment"><?php echo $this->Chart->formatPct($userTrackReviewSnapshot["liking_pct"]); ?><i class="fa fa-smile-o"></i></li>
                        <li class="neutral"><?php echo $this->Chart->formatPct($userTrackReviewSnapshot["neutral_pct"]); ?><i class="fa fa-meh-o"></i></li>
                        <li class="displeasure"><?php echo $this->Chart->formatPct($userTrackReviewSnapshot["disliking_pct"]); ?><i class="fa fa-frown-o"></i></li>
                    </ul>
                <?php else : ?>
                    <p><?php echo __("You have not reviewed this track yet."); ?></p>
                <?php endif; ?>
            </section>
            <?php endif; ?>
        </div>
    </article>

    <section class="recent-reviewers">
        <h2><?php echo __("Recent Reviewers"); ?></h2>
        <div class="container container-fluid">
            <section class="col-xs-12 col-md-4 tankers">
            <?php if(count($usersWhoReviewed) > 0) : ?>
                <ul>
                    <?php foreach($usersWhoReviewed as $user) : ?>
                    <li>
                        <?php echo $this->Html->link(
                                array_key_exists("image", $user["User"]) && !is_null($user["User"]["image"]) ?
                                    $this->Html->image($user["User"]["image"], array("alt" => $user["User"]["firstname"] . " " . $user["User"]["lastname"]))
                                    : $user["User"]["firstname"] . " " . $user["User"]["lastname"]
                                ,
                                array('controller' => 'tracks', 'action' => 'by_user', $track["slug"], $user["User"]["slug"]),
                                array("escape" => false)
                        ); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p><?php echo __("Be the first to review this track."); ?></p>
            <?php endif; ?>
            </section>

            <section class="col-xs-12 col-md-4 subscriptions">
                <?php if(isset($subsWhoReviewed) && count($subsWhoReviewed > 0)) : ?>
                    <?php if(count($subsWhoReviewed) > 0) : ?>
                        <ul>
                            <?php foreach($subsWhoReviewed as $idx => $user) : ?>
                            <li>
                                <?php
                                $name = $user["User"]["firstname"] . " " . $user["User"]["lastname"];
                                echo $this->Html->link(
                                        array_key_exists("image", $user["User"]) && !is_null($user["User"]["image"]) ?
                                            $this->Html->image($user["User"]["image"], array("alt" => $name))
                                            : $name
                                        ,
                                        array('controller' => 'tracks', 'action' => 'by_user', $track["slug"], $user["User"]["slug"]),
                                        array("escape" => false)
                                ); ?>
                            </li>
                            <?php if($idx >= 3 && (count($subsWhoReviewed) - 4 > 0)) :  ?>
                                <li class="others"><?php echo sprintf(__("+ %s others"), count($subsWhoReviewed) - 4); ?></li>
                            <?php break; endif; ?>
                            <?php endforeach; ?>
                        </ul>
                        <p>
                            <?php echo $this->Html->link(sprintf(__("%s of the people you are subscribed to reviewed %s."), count($subsWhoReviewed), $track["title"]),
                                array('controller' => 'tracks', 'action' => 'by_subscriptions', $track["slug"]));
                            ?>
                        </p>
                    <?php else : ?>
                        <p><?php echo __("None of your subscriptions have reviewed this track."); ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </section>

            <section class="col-xs-12 col-md-4 you">
                <?php echo $this->Html->link(__("Review track"), array('controller' => 'player', 'action' => 'play', $track["slug"])); ?>
            </section>
        </div>
    </section>

    <section class="track-player">
        <h2><?php echo __("Overview"); ?></h2>

        <?php // echo $this->element("player"); ?>
        <?php echo $this->element("graph"); ?>


    </section>

    <?php echo $this->Disqus->get('/artists/view/'.$artist["slug"].'/', $artist["name"]); ?>

</div>

<section class="credits">
    <div class="container container-fluid">
        <p>
            <?php echo __("Track description courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>.
            <?php echo __("It was last updated on"); ?> <?php echo date("F j, g:i a", $lastfmTrack["lastsync"]); ?>.
        </p>
    </div>
</section>

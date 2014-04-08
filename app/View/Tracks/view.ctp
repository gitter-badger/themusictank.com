
<nav class="sub-menu">
    <ul class="horizontal">
        <li><?php echo $this->Html->link(__("Artists"), array('controller' => 'artists', 'action' => 'index')); ?></li>
        <li><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></li>
        <li><?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?></li>
        <li><?php echo $this->Html->link($track["title"], array('controller' => 'tracks', 'action' => 'view', $track["slug"])); ?></li>
    </ul>
</nav>


<article class="heading track-profile">

    <div class="thumbnail">
        <?php echo $this->Html->image($album["image"], array("alt" => $album["name"])); ?>
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

        <?php if(isset($previousTrack)) : ?>
        <ol start="<?php echo $previousTrack["track_num"]; ?>">
        <?php else : ?>
        <ol start="<?php echo $track["track_num"]; ?>">
        <?php endif; ?>
            <?php if(isset($previousTrack)) : ?>
                <li>
                    <?php echo $this->Html->link($previousTrack["title"], array('controller' => 'tracks', 'action' => 'view', $previousTrack["slug"])); ?>
                </li>
            <?php endif; ?>
            <li>
                <?php echo $this->Html->link($track["title"], array('controller' => 'tracks', 'action' => 'view', $track["slug"])); ?>
            </li>
            <?php if(isset($nextTrack)) : ?>
                <li>
                    <?php echo $this->Html->link($nextTrack["title"], array('controller' => 'tracks', 'action' => 'view', $nextTrack["slug"])); ?>
                </li>
            <?php endif; ?>
        </ol>
    </aside>


    <div class="statistics">

        <section class="tankers">
            <?php echo $this->Chart->getBigPie("track", $track["slug"], $trackReviewSnapshot); ?>
            <h3><?php echo __("General"); ?></h3>  
            <ul>
                <li class="average"><?php echo $this->Chart->formatScore($trackReviewSnapshot["score_snapshot"]); ?></li>
                <li class="enjoyment"><?php echo $this->Chart->formatPct($trackReviewSnapshot["liking_pct"]); ?><br>:)</li>
                <li class="displeasure"><?php echo $this->Chart->formatPct($trackReviewSnapshot["disliking_pct"]); ?><br>:(</li>
            </ul>
        </section>

        <?php if(isset($subsAlbumReviewSnapshot)) : ?>
        <section class="subscribers">
            <?php if(count($subsTrackReviewSnapshot) > 0) : ?>
                <h3><?php echo __("Subscriptions"); ?></h3>  
                <?php echo $this->Chart->getBigPie("track", $track["slug"], $subsTrackReviewSnapshot); ?>
                <ul>
                    <li class="average"><?php echo $this->Chart->formatScore($subsTrackReviewSnapshot["score_snapshot"]); ?></li>
                    <li class="enjoyment"><?php echo $this->Chart->formatPct($subsTrackReviewSnapshot["liking_pct"]); ?><br>:)</li>
                    <li class="displeasure"><?php echo $this->Chart->formatPct($subsTrackReviewSnapshot["disliking_pct"]); ?><br>:(</li>
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
                    <li class="enjoyment"><?php echo $this->Chart->formatPct($userTrackReviewSnapshot["liking_pct"]); ?><br>:)</li>
                    <li class="displeasure"><?php echo $this->Chart->formatPct($userTrackReviewSnapshot["disliking_pct"]); ?><br>:(</li>
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
    <?php if(count($usersWhoReviewed > 0)) : ?>
        <section class="tankers">
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
        </section>
    <?php endif; ?>

    <?php if(count($subsWhoReviewed > 0)) : ?>
        <section class="subscriptions">
            <p>
                <?php echo $this->Html->link(sprintf(__("%s of the people you are subscribed to reviewed %s."), count($subsWhoReviewed), $track["title"]),
                    array('controller' => 'tracks', 'action' => 'by_subscriptions', $track["slug"])); 
                ?>
            </p>                
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
        </section>
    <?php endif; ?>
</div>


<?php echo $this->element("player"); ?>

<p class="credits">
    <?php echo __("Track description courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>. 
    <?php echo __("It was last updated on"); ?> <?php echo date("F j, g:i a", $lastfmTrack["lastsync"]); ?>. 
</p>

<?php echo $this->Disqus->get('/artists/view/'.$artist["slug"].'/', $artist["name"]); ?>


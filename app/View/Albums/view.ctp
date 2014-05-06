<nav class="sub-menu">
    <ol class="breadcrumb">
        <li><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></li>
        <li class="active"><?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?></li>
    </ol>
</nav>

<article class="heading album-profile">

    <div class="thumbnail">
        <?php echo $this->Html->image( $this->App->getImageUrl($album), array("alt" => $album["name"])); ?>                                
    </div>

    <aside>
        <h1><?php echo $album["name"]; ?></h1>        
        <time datetime="<?php echo date("c", $album["release_date"]); ?>"><?php echo __("Released"); ?> <?php echo date("F j Y", $album["release_date"]); ?></time>
        <section class="description expandable">
            <div class="wrapper">
                <?php echo $lastfmAlbum["wiki"]; ?>
            </div>        
        </section>
    </aside>

    <div class="statistics">

        <section class="tankers">
            <?php echo $this->Chart->getBigPie("album", $album["slug"] . "-1", $albumReviewSnapshot); ?>
            <h3><?php echo __("General"); ?></h3>  
            <?php /* <p><?php echo __("Metacritic score"); ?> <?php echo $this->Chart->formatScore($albumReviewSnapshot["metacritic_score"]); ?></p> */ ?>
            <ul>
                <li class="average"><?php echo $this->Chart->formatScore($albumReviewSnapshot["score_snapshot"]); ?></li>
                <li class="enjoyment"><?php echo $this->Chart->formatPct($albumReviewSnapshot["liking_pct"]); ?><i class="fa fa-smile-o"></i></li>                
                <li class="neutral"><?php echo $this->Chart->formatPct($albumReviewSnapshot["neutral_pct"]); ?><i class="fa fa-meh-o"></i></li>
                <li class="displeasure"><?php echo $this->Chart->formatPct($albumReviewSnapshot["disliking_pct"]); ?><i class="fa fa-frown-o"></i></li>
            </ul>
        </section>

        <section class="subscribers">
        <?php if(isset($subsAlbumReviewSnapshot)) : ?>
                <?php echo $this->Chart->getBigPie("album", $album["slug"] . "-2", $albumReviewSnapshot); ?>  
            <?php if(count($subsAlbumReviewSnapshot) > 0) : ?>    
                <h3><?php echo __("Subscriptions"); ?></h3>  
                <ul>
                     <li class="average"><?php echo $this->Chart->formatScore($albumReviewSnapshot["score_snapshot"]); ?></li>
                    <li class="enjoyment"><?php echo $this->Chart->formatPct($subsAlbumReviewSnapshot["liking_pct"]); ?><i class="fa fa-smile-o"></i></li>                
                    <li class="neutral"><?php echo $this->Chart->formatPct($subsAlbumReviewSnapshot["neutral_pct"]); ?><i class="fa fa-meh-o"></i></li>
                    <li class="displeasure"><?php echo $this->Chart->formatPct($subsAlbumReviewSnapshot["disliking_pct"]); ?><i class="fa fa-frown-o"></i></li>
                </ul>
            <?php else : ?>
                <p><?php echo __("None of the people you are subscribed to have reviewed this album yet."); ?></p>
            <?php endif; ?>
        <?php endif; ?>
        </section>

        <section class="you">
        <?php if(isset($userAlbumReviewSnapshot)) : ?>
            <?php echo $this->Chart->getBigPie("album", $album["slug"] . "-3", $userAlbumReviewSnapshot); ?>  
            <?php if(count($userAlbumReviewSnapshot) > 0) : ?> 
                <h3><?php echo __("Your rating"); ?></h3>  
                <ul>
                    <li class="average"><?php echo $this->Chart->formatScore($userAlbumReviewSnapshot["score_snapshot"]); ?></li>
                    <li class="enjoyment"><?php echo $this->Chart->formatPct($userAlbumReviewSnapshot["liking_pct"]); ?><i class="fa fa-smile-o"></i></li>                
                    <li class="neutral"><?php echo $this->Chart->formatPct($userAlbumReviewSnapshot["neutral_pct"]); ?><i class="fa fa-meh-o"></i></li>
                    <li class="displeasure"><?php echo $this->Chart->formatPct($userAlbumReviewSnapshot["disliking_pct"]); ?><i class="fa fa-frown-o"></i></li>
                </ul>
            <?php else : ?>
                <p><?php echo __("You have not reviewed this album yet."); ?></p>
            <?php endif; ?>
        <?php endif; ?>
        </section>
    </div>

</article>


<section class="recent-reviewers">
    <h2><?php echo __("Recent Reviewers"); ?></h2>
    <div class="container container-fluid">
        <section class="col-xs-12 col-md-6 tankers">
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

        <section class="col-xs-12 col-md-6 subscriptions">
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
    </div>
</section>


<section class="album-overview">
    <h2><?php echo __("Overview"); ?></h2>
    <?php echo $this->element("albumGraph"); ?>
</section>

<section class="track-details">
    <h2><?php echo __("Detailed track data"); ?></h2>
    <?php if(count($tracks) > 0) : ?> 
        <ol class="tracks">            
        <?php  foreach($tracks as $track) : ?>
            <li>
                <?php echo $this->Html->link($track["title"], array('controller' => 'tracks', 'action' => 'view', $track["slug"])); ?>                
                <?php echo $this->Chart->getHorizontalGraph("track", $track["slug"] . "-1", $track["TrackReviewSnapshot"]); ?>
            </li>
        <?php endforeach; ?>
        </ol>        
    <?php else : ?>        
        <p><?php echo __("Sorry for the inconvenience, but we could not fetch the tracks."); ?></p>            
    <?php endif; ?>    
</section>

<?php echo $this->Disqus->get('/artists/view/'.$artist["slug"].'/', $artist["name"]); ?>

<p class="credits">
    <?php echo __("Album description courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>. 
    <?php echo __("They were last updated on"); ?> <?php echo date("F j, g:i a", $lastfmAlbum["lastsync"]); ?>. 
    <?php echo __("Album tracks and image courtesy of"); ?> <?php echo $this->Html->link("Rdio.com", "http://www.rdio.com/", array("target" => "_blank")); ?>.
</p>

<nav class="sub-menu">
    <ul class="horizontal">
        <li><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></li>
        <li><?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?></li>
    </ul>

    <div class="search">
        <form action="/search/" method="get"><input type="text" name="q" value="" placeholder="Search..." /><input type="submit" name="Go" /></form>
    </div>
</nav>

<article class="heading album-profile">

    <div class="thumbnail">
        <?php echo $this->Html->image($album["image"], array("alt" => $album["name"])); ?>                                
    </div>

    <aside>
        <h1><?php echo $artist["name"]; ?></h1>        
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
            <!-- <p><?php echo __("Metacritic score"); ?> <?php echo $this->Chart->formatScore($albumReviewSnapshot["metacritic_score"]); ?></p> -->
            <ul>
                <li class="average"><?php echo $this->Chart->formatScore($albumReviewSnapshot["score_snapshot"]); ?></li>
                <li class="enjoyment"><?php echo $this->Chart->formatPct($albumReviewSnapshot["liking_pct"]); ?><br>:)</li>
                <li class="displeasure"><?php echo $this->Chart->formatPct($albumReviewSnapshot["disliking_pct"]); ?><br>:(</li>
            </ul>
        </section>

        <?php if(isset($subsAlbumReviewSnapshot)) : ?>
        <section class="subscribers">
                <?php echo $this->Chart->getBigPie("album", $album["slug"] . "-2", $userAlbumReviewSnapshot); ?>  
            <?php if(count($subsAlbumReviewSnapshot) > 0) : ?>    
                <h3><?php echo __("Subscriptions"); ?></h3>  
                <ul>
                    <li class="average"><?php echo $this->Chart->formatScore($subsAlbumReviewSnapshot["score_snapshot"]); ?></li>
                    <li class="enjoyment"><?php echo $this->Chart->formatPct($subsAlbumReviewSnapshot["liking_pct"]); ?><br>:)</li>
                    <li class="displeasure"><?php echo $this->Chart->formatPct($subsAlbumReviewSnapshot["disliking_pct"]); ?><br>:(</li>
                </ul>
            <?php else : ?>
                <p><?php echo __("None of the people you are subscribed to have reviewed this album yet."); ?></p>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <?php if(isset($userAlbumReviewSnapshot)) : ?>
        <section class="you">
            <?php echo $this->Chart->getBigPie("album", $album["slug"] . "-3", $userAlbumReviewSnapshot); ?>  
            <?php if(count($userAlbumReviewSnapshot) > 0) : ?> 
                <h3><?php echo __("Your rating"); ?></h3>  
                <ul>
                    <li class="average"><?php echo $this->Chart->formatScore($userAlbumReviewSnapshot["score_snapshot"]); ?></li>
                    <li class="enjoyment"><?php echo $this->Chart->formatPct($userAlbumReviewSnapshot["liking_pct"]); ?><br>:)</li>
                    <li class="displeasure"><?php echo $this->Chart->formatPct($userAlbumReviewSnapshot["disliking_pct"]); ?><br>:(</li>
                </ul>
            <?php else : ?>
                <p><?php echo __("You have not reviewed this album yet."); ?></p>
            <?php endif; ?>
        </section>
        <?php endif; ?>
    </div>

</article>

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

<p class="credits">
    <?php echo __("Album description courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>. 
    <?php echo __("They were last updated on"); ?> <?php echo date("F j, g:i a", $lastfmAlbum["lastsync"]); ?>. 
    <?php echo __("Album tracks and image courtesy of"); ?> <?php echo $this->Html->link("Rdio.com", "http://www.rdio.com/", array("target" => "_blank")); ?>.
</p>

<?php echo $this->Disqus->get('/artists/view/'.$artist["slug"].'/', $artist["name"]); ?>

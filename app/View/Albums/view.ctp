
<nav class="sub-menu">
    <ul class="horizontal">
        <li><?php echo $this->Html->link(__("Artists"), array('controller' => 'artists', 'action' => 'index')); ?></li>
        <li><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></li>
        <li><?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?></li>
    </ul>
</nav>


<article class="heading track-profile">

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
            <h3><?php echo __("Everyone"); ?></h3>
            <p><?php echo __("Average user score"); ?> <?php echo $this->Chart->formatScore($albumReviewSnapshot["score_snapshot"]); ?></p>        
            <?php $enjoymentTimes =  $this->Chart->getEnjoymentTime($albumReviewSnapshot, (int)$album["duration"]); ?>
            <p><?php echo __("Metacritic score"); ?> <?php echo $this->Chart->formatScore($albumReviewSnapshot["metacritic_score"]); ?></p>
            <p><?php echo __("Enjoyment"); ?> <?php echo $albumReviewSnapshot["liking_pct"]; ?> %</p>
            <p><?php echo __("Disliking"); ?> <?php echo $albumReviewSnapshot["disliking_pct"]; ?> %</p>
            <p><?php echo __("Enjoyment time"); ?> <?php echo $enjoymentTimes["liking"]; ?></p>
            <p><?php echo __("Time disliked"); ?> <?php echo $enjoymentTimes["disliking"]; ?></p>
            <?php echo $this->Chart->getBigPie("album", $album["slug"], $albumReviewSnapshot); ?>
        </section>

        <?php if(isset($userAlbumReviewSnapshot)) : ?>
        <section class="you">
            <?php if(count($userAlbumReviewSnapshot) > 0) : ?>    
                <h3><?php echo __("Your opinion"); ?></h3>
                <p><?php echo __("Average subscriber score"); ?> <?php echo $this->Chart->formatScore($userAlbumReviewSnapshot["score_snapshot"]); ?></p>        
                <?php $enjoymentTimes =  $this->Chart->getEnjoymentTime($userAlbumReviewSnapshot, (int)$album["duration"]); ?>
                <p><?php echo __("Enjoyment"); ?> <?php echo $userAlbumReviewSnapshot["liking_pct"]; ?> %</p>
                <p><?php echo __("Disliking"); ?> <?php echo $userAlbumReviewSnapshot["disliking_pct"]; ?> %</p>
                <p><?php echo __("Enjoyment time"); ?> <?php echo $enjoymentTimes["liking"]; ?></p>
                <p><?php echo __("Time disliked"); ?> <?php echo $enjoymentTimes["disliking"]; ?></p>
                <?php echo $this->Chart->getBigPie("album", $album["slug"], $userAlbumReviewSnapshot); ?>
            <?php else : ?>
                <p><?php echo __("You have not reviewed this album yet."); ?></p>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <?php if(isset($subsAlbumReviewSnapshot)) : ?>
        <section class="subscribers">
            <?php if(count($subsAlbumReviewSnapshot) > 0) : ?>    
                <h3><?php echo __("People you are subscribed to"); ?></h3>
                <p><?php echo __("Average subscriber score"); ?> <?php echo $this->Chart->formatScore($subsAlbumReviewSnapshot["score_snapshot"]); ?></p>        
                <?php $enjoymentTimes =  $this->Chart->getEnjoymentTime($subsAlbumReviewSnapshot, (int)$album["duration"]); ?>
                <p><?php echo __("Enjoyment"); ?> <?php echo $subsAlbumReviewSnapshot["liking_pct"]; ?> %</p>
                <p><?php echo __("Disliking"); ?> <?php echo $subsAlbumReviewSnapshot["disliking_pct"]; ?> %</p>
                <p><?php echo __("Enjoyment time"); ?> <?php echo $enjoymentTimes["liking"]; ?></p>
                <p><?php echo __("Time disliked"); ?> <?php echo $enjoymentTimes["disliking"]; ?></p>
                <?php echo $this->Chart->getBigPie("album", $album["slug"], $subsAlbumReviewSnapshot); ?>
            <?php else : ?>
                <p><?php echo __("None of the people you are subscribed to have reviewed this album yet."); ?></p>
            <?php endif; ?>
        </section>
        <?php endif; ?>

    </div>

</article>


<h2><?php echo __("Overview"); ?></h2>
<?php echo $this->element("albumGraph"); ?>


<?php /*
<h2><?php echo __("Detailed track data"); ?></h2>
<?php if(count($tracks) > 0) : ?> 
    <ol class="tracks">            
    <?php  foreach($tracks as $track) : ?>
        <li>
            <?php echo $this->Html->link($track["title"], array('controller' => 'tracks', 'action' => 'view', $track["slug"])); ?>
            [<?php echo $this->Html->link(__("View stats"), array('controller' => 'tracks', 'action' => 'view', $track["slug"])); ?>]
            [<?php echo $this->Html->link(__("Review"), array('controller' => 'player', 'action' => 'play', $track["slug"])); ?>]
        </li>
    <?php endforeach; ?>
    </ol>        
<?php else : ?>        
    <p><?php echo __("Sorry for the inconvenience, but we could not fetch the tracks."); ?></p>            
<?php endif; ?>    


*/ ?>

<?php echo $this->Disqus->get('/artists/view/'.$artist["slug"].'/', $artist["name"]); ?>

<p class="credits">
    <?php echo __("Album description courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>. 
    <?php echo __("They were last updated on"); ?> <?php echo date("F j, g:i a", $lastfmAlbum["lastsync"]); ?>. 
    <?php echo __("Album tracks and image courtesy of"); ?> <?php echo $this->Html->link("Rdio.com", "http://www.rdio.com/", array("target" => "_blank")); ?>.
</p>
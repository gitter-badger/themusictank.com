<article class="cols">
    <div class="col col-1-2">    
        <header>
            <h2><?php echo $album["name"]; ?></h2>
            <h3>
                <?php echo __("By"); ?>
                <?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?>    
                <?php echo __("released"); ?> <?php echo date("F j Y", $album["release_date"]); ?>
            </h3>
        </header>      
        
        <?php if(!is_null($album["image"])) : ?>
            <?php echo $this->Html->image($album["image"], array("alt" => $album["name"], "class" => "thumbnail")); ?>
        <?php endif; ?>
        
        <div class="description expandable">
            <div class="wrapper">
                <?php echo $lastfmAlbum["wiki"]; ?>
            </div>
        </div>
    </div>
    <div class="col col-2-2">              
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
    </div>    
</article>

<?php echo $this->element("albumGraph"); ?>

<section class="statistics">
    <h3><?php echo __("Everyone"); ?></h3>
    <p><?php echo __("Average user score"); ?> <?php echo $this->Chart->formatScore($albumReviewSnapshot["score_snapshot"]); ?></p>        
    <?php $enjoymentTimes =  $this->Chart->getEnjoymentTime($albumReviewSnapshot, (int)$track["duration"]); ?>
    <p><?php echo __("Metacritic score"); ?> <?php echo $this->Chart->formatScore($albumReviewSnapshot["metacritic_score"]); ?></p>
    <p><?php echo __("Enjoyment"); ?> <?php echo $albumReviewSnapshot["liking_pct"]; ?> %</p>
    <p><?php echo __("Disliking"); ?> <?php echo $albumReviewSnapshot["disliking_pct"]; ?> %</p>
    <p><?php echo __("Enjoyment time"); ?> <?php echo $enjoymentTimes["liking"]; ?></p>
    <p><?php echo __("Time disliked"); ?> <?php echo $enjoymentTimes["disliking"]; ?></p>
    <?php echo $this->Chart->getBigPie("track", $track["slug"], $albumReviewSnapshot); ?>
</section>

<?php if(isset($userAlbumReviewSnapshot)) : ?>
<section class="statistics subscribers">
    <?php if(count($userAlbumReviewSnapshot) > 0) : ?>    
        <h3><?php echo __("Your opinion"); ?></h3>
        <p><?php echo __("Average subscriber score"); ?> <?php echo $this->Chart->formatScore($userAlbumReviewSnapshot["score_snapshot"]); ?></p>        
        <?php $enjoymentTimes =  $this->Chart->getEnjoymentTime($userAlbumReviewSnapshot, (int)$track["duration"]); ?>
        <p><?php echo __("Enjoyment"); ?> <?php echo $userAlbumReviewSnapshot["liking_pct"]; ?> %</p>
        <p><?php echo __("Disliking"); ?> <?php echo $userAlbumReviewSnapshot["disliking_pct"]; ?> %</p>
        <p><?php echo __("Enjoyment time"); ?> <?php echo $enjoymentTimes["liking"]; ?></p>
        <p><?php echo __("Time disliked"); ?> <?php echo $enjoymentTimes["disliking"]; ?></p>
        <?php echo $this->Chart->getBigPie("track", $track["slug"], $userAlbumReviewSnapshot); ?>
    <?php else : ?>
        <p><?php echo __("You have not reviewed this album yet."); ?></p>
    <?php endif; ?>
</section>
<?php endif; ?>

<?php if(isset($subsAlbumReviewSnapshot)) : ?>
<section class="statistics subscribers">
    <?php if(count($subsAlbumReviewSnapshot) > 0) : ?>    
        <h3><?php echo __("People you are subscribed to"); ?></h3>
        <p><?php echo __("Average subscriber score"); ?> <?php echo $this->Chart->formatScore($subsAlbumReviewSnapshot["score_snapshot"]); ?></p>        
        <?php $enjoymentTimes =  $this->Chart->getEnjoymentTime($subsAlbumReviewSnapshot, (int)$track["duration"]); ?>
        <p><?php echo __("Enjoyment"); ?> <?php echo $subsAlbumReviewSnapshot["liking_pct"]; ?> %</p>
        <p><?php echo __("Disliking"); ?> <?php echo $subsAlbumReviewSnapshot["disliking_pct"]; ?> %</p>
        <p><?php echo __("Enjoyment time"); ?> <?php echo $enjoymentTimes["liking"]; ?></p>
        <p><?php echo __("Time disliked"); ?> <?php echo $enjoymentTimes["disliking"]; ?></p>
        <?php echo $this->Chart->getBigPie("track", $track["slug"], $subsAlbumReviewSnapshot); ?>
    <?php else : ?>
        <p><?php echo __("None of the people you are subscribed to have reviewed this album yet."); ?></p>
    <?php endif; ?>
</section>
<?php endif; ?>


<p class="credits">
    <?php echo __("Album description courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>. 
    <?php echo __("They were last updated on"); ?> <?php echo date("F j, g:i a", $lastfmAlbum["lastsync"]); ?>. 
    <?php echo __("Album tracks and image courtesy of"); ?> <?php echo $this->Html->link("Rdio.com", "http://www.rdio.com/", array("target" => "_blank")); ?>.
</p>

<?php echo $this->Disqus->get('/artists/view/'.$artist["slug"].'/', $artist["name"]); ?>
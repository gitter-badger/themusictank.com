<header>
    <h2><?php echo $track["title"]; ?></h2>
    <h3>
        <?php echo __("By"); ?>
        <?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?>,
        <?php echo __("found on"); ?>
        <?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?>
    </h3>
</header>

<p><?php echo $lastfmTrack["wiki"]; ?></p>

<?php echo $this->element("player"); ?>

<section class="statistics">
    <p><?php echo __("Average user score"); ?> <?php echo $this->Chart->formatScore($trackReviewSnapshot["score_snapshot"]); ?></p>
        
    <?php $enjoymentTimes =  $this->Chart->getEnjoymentTime($trackReviewSnapshot, (int)$track["duration"]); ?>
    <p><?php echo __("Enjoyment"); ?> <?php echo $trackReviewSnapshot["liking_pct"]; ?> %</p>
    <p><?php echo __("Disliking"); ?> <?php echo $trackReviewSnapshot["disliking_pct"]; ?> %</p>
    <p><?php echo __("Enjoyment time"); ?> <?php echo $enjoymentTimes["liking"]; ?></p>
    <p><?php echo __("Time disliked"); ?> <?php echo $enjoymentTimes["disliking"]; ?></p>
    <p><?php echo __("Total time");?> <?php echo date("i:s", (int)$track["duration"]); ?></p>
</section>


<?php /*
<section class="graphs cols">
    <div class="col col-1-3">
        <h3><?php echo __("Appreciation"); ?></h3>
        <?php echo $this->Chart->getBigPie("track", $track["slug"], $snapshot); ?>
    </div>
    <div class="col col-23-3">    
        <h3><?php echo __("Track Groove"); ?></h3>        
        <?php echo $this->Chart->getTrackChart($track["slug"], $snapshot); ?>    
    </div>
</div> */ ?>

<p class="credits">
    <?php echo __("Track description courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>. 
    <?php echo __("It was last updated on"); ?> <?php echo date("F j, g:i a", $lastfmTrack["lastsync"]); ?>. 
</p>

<?php echo $this->Disqus->get('/artists/view/'.$artist["slug"].'/', $artist["name"]); ?>


<header>
    <h2><?php echo $track["title"]; ?></h2>
    <h3>
        <?php echo __("By"); ?>
        <?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?>,
        <?php echo __("found on"); ?>
        <?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?>
    </h3>
</header>

<?php echo $lastfmTrack["wiki"]; ?>

<?php echo $this->element("player"); ?>

<section class="statistics">
    <h3><?php echo __("Everyone"); ?></h3>
    <p><?php echo __("Average user score"); ?> <?php echo $this->Chart->formatScore($trackReviewSnapshot["score_snapshot"]); ?></p>        
    <?php $enjoymentTimes =  $this->Chart->getEnjoymentTime($trackReviewSnapshot, (int)$track["duration"]); ?>
    <p><?php echo __("Enjoyment"); ?> <?php echo $trackReviewSnapshot["liking_pct"]; ?> %</p>
    <p><?php echo __("Disliking"); ?> <?php echo $trackReviewSnapshot["disliking_pct"]; ?> %</p>
    <p><?php echo __("Enjoyment time"); ?> <?php echo $enjoymentTimes["liking"]; ?></p>
    <p><?php echo __("Time disliked"); ?> <?php echo $enjoymentTimes["disliking"]; ?></p>
    <?php echo $this->Chart->getBigPie("track", $track["slug"], $trackReviewSnapshot); ?>
</section>

<?php if(isset($userTrackReviewSnapshot)) : ?>
<section class="statistics subscribers">
    <?php if(count($userTrackReviewSnapshot) > 0) : ?>
        <h3><?php echo __("You"); ?></h3>
        <p><?php echo __("Average subscriber score"); ?> <?php echo $this->Chart->formatScore($userTrackReviewSnapshot["score_snapshot"]); ?></p>        
        <?php $enjoymentTimes =  $this->Chart->getEnjoymentTime($userTrackReviewSnapshot, (int)$track["duration"]); ?>
        <p><?php echo __("Enjoyment"); ?> <?php echo $userTrackReviewSnapshot["liking_pct"]; ?> %</p>
        <p><?php echo __("Disliking"); ?> <?php echo $userTrackReviewSnapshot["disliking_pct"]; ?> %</p>
        <p><?php echo __("Enjoyment time"); ?> <?php echo $enjoymentTimes["liking"]; ?></p>
        <p><?php echo __("Time disliked"); ?> <?php echo $enjoymentTimes["disliking"]; ?></p>
        <?php echo $this->Chart->getBigPie("track", $track["slug"], $userTrackReviewSnapshot); ?>
    <?php else : ?>
        <p><?php echo __("You have not reviewed this track yet."); ?></p>
    <?php endif; ?>
</section>
<?php endif; ?>

<?php if(isset($subsTrackReviewSnapshot)) : ?>
<section class="statistics subscribers">
    <?php if(count($subsTrackReviewSnapshot) > 0) : ?>
        <h3><?php echo __("People you are subscribed to"); ?></h3>
        <p><?php echo __("Average subscriber score"); ?> <?php echo $this->Chart->formatScore($subsTrackReviewSnapshot["score_snapshot"]); ?></p>        
        <?php $enjoymentTimes =  $this->Chart->getEnjoymentTime($subsTrackReviewSnapshot, (int)$track["duration"]); ?>
        <p><?php echo __("Enjoyment"); ?> <?php echo $subsTrackReviewSnapshot["liking_pct"]; ?> %</p>
        <p><?php echo __("Disliking"); ?> <?php echo $subsTrackReviewSnapshot["disliking_pct"]; ?> %</p>
        <p><?php echo __("Enjoyment time"); ?> <?php echo $enjoymentTimes["liking"]; ?></p>
        <p><?php echo __("Time disliked"); ?> <?php echo $enjoymentTimes["disliking"]; ?></p>
        <?php echo $this->Chart->getBigPie("track", $track["slug"], $subsTrackReviewSnapshot); ?>
    <?php else : ?>
        <p><?php echo __("None of the people you are subscribed to have reviewed this track yet."); ?></p>
    <?php endif; ?>
</section>
<?php endif; ?>


<p class="credits">
    <?php echo __("Track description courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>. 
    <?php echo __("It was last updated on"); ?> <?php echo date("F j, g:i a", $lastfmTrack["lastsync"]); ?>. 
</p>

<?php echo $this->Disqus->get('/artists/view/'.$artist["slug"].'/', $artist["name"]); ?>


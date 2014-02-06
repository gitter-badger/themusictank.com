<header>
    <h2><?php echo $track["title"]; ?></h2>
    <h3>
        <?php echo __("By"); ?>
        <?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?>,
        <?php echo __("found on"); ?>
        <?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?>
        <?php echo __("reviewed by"); ?>
        <?php echo $this->Html->link($user["firstname"] . " " . $user["lastname"], array('controller' => 'profile', 'action' => 'view', $user["slug"])); ?>
    </h3>
</header>

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
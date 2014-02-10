<header>
    <h2>
        <?php echo $this->Html->link($track["title"], array('controller' => 'tracks', 'action' => 'view', $track["slug"])); ?>,
    </h2>
    <h3>
        <?php echo __("By"); ?>
        <?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?>,
        <?php echo __("found on"); ?>
        <?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?>
        <?php echo __("reviewed by"); ?>
        <?php echo $this->Html->link($viewingUser["firstname"] . " " . $viewingUser["lastname"], array('controller' => 'profile', 'action' => 'view', $viewingUser["slug"])); ?>
    </h3>
    <?php $currentPage = "http://" . $_SERVER['SERVER_NAME'] . Router::url(array('controller' => 'tracks', 'action' => 'by_user', $track["slug"], $viewingUser["slug"])); ?>
    <div class="share">                
        <a href="https://twitter.com/share" class="twitter-share-button" 
           data-url="<?php echo $currentPage; ?>" 
           data-text="<?php echo sprintf(__("%s's review of '%s' on @themusictank : "), $viewingUser["firstname"] . " " . $viewingUser["lastname"], $track["title"]); ?>"
           data-lang="en">Tweet</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>        
    
        <div class="fb-share-button" data-href="<?php echo $currentPage; ?>" data-type="button_count"></div>
        <div id="fb-root"></div>
        <script>(function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=497725690321176";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
    </div>
    
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
<section class="statistics you">
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


<?php if(isset($viewingTrackReviewSnapshot)) : ?>
<section class="statistics user">
    <?php if(count($viewingTrackReviewSnapshot) > 0) : ?>
        <h3><?php echo __("You"); ?></h3>
        <p><?php echo __("Average subscriber score"); ?> <?php echo $this->Chart->formatScore($viewingTrackReviewSnapshot["score_snapshot"]); ?></p>        
        <?php $enjoymentTimes =  $this->Chart->getEnjoymentTime($viewingTrackReviewSnapshot, (int)$track["duration"]); ?>
        <p><?php echo __("Enjoyment"); ?> <?php echo $viewingTrackReviewSnapshot["liking_pct"]; ?> %</p>
        <p><?php echo __("Disliking"); ?> <?php echo $viewingTrackReviewSnapshot["disliking_pct"]; ?> %</p>
        <p><?php echo __("Enjoyment time"); ?> <?php echo $enjoymentTimes["liking"]; ?></p>
        <p><?php echo __("Time disliked"); ?> <?php echo $enjoymentTimes["disliking"]; ?></p>
        <?php echo $this->Chart->getBigPie("track", $track["slug"], $userTrackReviewSnapshot); ?>
    <?php else : ?>
        <p><?php echo __("You have not reviewed this track yet."); ?></p>
    <?php endif; ?>
</section>
<?php endif; ?>
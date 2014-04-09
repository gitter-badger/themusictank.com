<article class="heading track-profile">
    <div class="thumbnail">
        <?php echo $this->Html->image($album["image"], array("alt" => $album["name"])); ?>
    </div>

    <aside>
        <h1><?php echo $track["title"]; ?></h1>
        <h2><?php echo sprintf(__("Off of %s, by %s"), $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])), $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])));?> </h2>
        <p>
            <?php echo __("Reviewed by"); ?>
            <?php echo $this->Html->link($viewingUser["firstname"] . " " . $viewingUser["lastname"], array('controller' => 'profiles', 'action' => 'view', $viewingUser["slug"])); ?>
            <?php echo $this->element('followButton', array("user" => $viewingUser)); ?>
        </p>

        <?php $currentPage = "http://" . $_SERVER['SERVER_NAME'] . Router::url(array('controller' => 'tracks', 'action' => 'by_user', $track["slug"], $viewingUser["slug"])); ?>
        <div class="share">                
            <a href="https://twitter.com/share" class="twitter-share-button" 
               data-url="<?php echo $currentPage; ?>" 
               data-text="<?php echo sprintf(__("%s's review of '%s' on @themusictank : "), $viewingUser["firstname"] . " " . $viewingUser["lastname"], $track["title"]); ?>"
               data-lang="en">Tweet</a>    
            <div class="fb-share-button" data-href="<?php echo $currentPage; ?>" data-type="button_count"></div>       
        </div>
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

        <section class="tankers">
            <?php echo $this->Chart->getBigPie("track", $track["slug"], $viewingTrackReviewSnapshot); ?>
            <h3><?php echo $viewingUser["firstname"] . " " . $viewingUser["lastname"]; ?></h3>  
            <ul>
                <li class="average"><?php echo $this->Chart->formatScore($viewingTrackReviewSnapshot["score_snapshot"]); ?></li>
                <li class="enjoyment"><?php echo $this->Chart->formatPct($viewingTrackReviewSnapshot["liking_pct"]); ?><br>:)</li>
                <li class="displeasure"><?php echo $this->Chart->formatPct($viewingTrackReviewSnapshot["disliking_pct"]); ?><br>:(</li>
            </ul>
        </section>        

        <?php if(isset($userTrackReviewSnapshot)) : ?>
            <section class="you">
                <?php echo $this->Chart->getBigPie("track", $track["slug"], $viewingTrackReviewSnapshot); ?>
                <h3><?php echo __("You"); ?></h3>
                <ul>
                    <li class="average"><?php echo $this->Chart->formatScore($userTrackReviewSnapshot["score_snapshot"]); ?></li>
                    <li class="enjoyment"><?php echo $this->Chart->formatPct($userTrackReviewSnapshot["liking_pct"]); ?><br>:)</li>
                    <li class="displeasure"><?php echo $this->Chart->formatPct($userTrackReviewSnapshot["disliking_pct"]); ?><br>:(</li>
                </ul>
            </section>
        <?php endif; ?>
    </div>
</article>

<?php echo $this->element("player"); ?>

<div id="fb-root"></div>
<script>
(function(d,s,id){ var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=497725690321176";fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));       
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
</script>
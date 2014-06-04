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
        <?php echo $this->element("stats"); ?>
    </div>
</article>

<div class="container">

        <?php // echo $this->element("player"); ?>
        <?php echo $this->element("graph"); ?>

</div>


<div id="fb-root"></div>
<script>
(function(d,s,id){ var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=497725690321176";fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
</script>

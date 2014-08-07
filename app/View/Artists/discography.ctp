<?php
    $isLogged = $this->Session->check('Auth.User.User.id');
?>
<div class="header-wrapper">
    <?php echo $this->Html->image( $this->App->getImageUrl($lastfmArtist, "blur"), array("alt" => $artist["name"], "class" => "blurred"));  ?>
    <?php echo $this->Html->image( $this->App->getImageUrl($lastfmArtist, "big"), array("alt" => $artist["name"], "class" => "clean"));  ?>
    <i class="mask"></i>
</div>

<article class="container container-fluid">

    <header>
        <?php $img = $this->Html->image( $this->App->getImageUrl($lastfmArtist), array("alt" => $artist["name"], "class" => "thumbnail")); ?>
        <?php echo $this->Html->link($img, array('controller' => 'albums', 'action' => 'view', $artist["slug"]), array("escape" => false)); ?>

        <h1><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></h1>

        <div class="score">
            <?php if(!is_null($artistReviewSnapshot["score"])) : ?>
                <?php echo (int)($artistReviewSnapshot["score"] * 100); ?>%
            <?php else : ?>
                N/A
            <?php endif; ?>
            <span><?php echo __("Score"); ?></span>
        </div>

        <div class="everyone piechart"></div>

        <div class="row stats">

            <div class="col-md-2 enjoyment">
                <span><?php echo __("Enjoyed"); ?></span>
                <em>
                    <?php if($this->Chart->isNotAvailable($artistReviewSnapshot)) : ?>
                        N/A
                    <?php else : ?>
                        <?php echo (int)$artistReviewSnapshot["liking_pct"]; ?>%
                    <?php endif; ?>
                </em>
            </div>
            <div class="col-md-2 dislike">
                <span><?php echo __("Disliked"); ?></span>
                <em>
                    <?php if($this->Chart->isNotAvailable($artistReviewSnapshot)) : ?>
                        N/A
                    <?php else : ?>
                        <?php echo (int)$artistReviewSnapshot["disliking_pct"]; ?>%
                    <?php endif; ?>
                </em>
            </div>

            <?php if(isset($bestTrack)) : ?>
            <div class="col-md-4 best-track">
                <span><?php echo __("Best track"); ?></span>
                <em><?php echo $this->Html->link($bestTrack["title"], array('controller' => 'tracks', 'action' => 'view', $bestTrack["slug"])); ?>&nbsp;<?php echo (int)$bestTrack["TrackReviewSnapshot"]["liking_pct"]; ?>%</em>
            </div>
            <?php endif; ?>

            <?php if(isset($worstTrack)) : ?>
            <div class="col-md-4 worst-track">
                <span><?php echo __("Worst track"); ?></span>
                <em><?php echo $this->Html->link($worstTrack["title"], array('controller' => 'tracks', 'action' => 'view', $worstTrack["slug"])); ?>&nbsp;<?php echo (int)$worstTrack["TrackReviewSnapshot"]["liking_pct"]; ?>%</em>
            </div>
            <?php endif; ?>

            <div class="col-md-12 social">

                <?php if($isLogged) : ?>
                    <div class="col-md-3">
                        <h3><?php echo __("Your subscriptions"); ?></h3>
                        <?php if($this->Chart->hasScore($subsArtistReviewSnapshot)) : ?>
                            <?php echo (int)($subsArtistReviewSnapshot["score"] * 100); ?>%
                        <?php else : ?>
                            N/A
                        <?php endif; ?>
                        <span><?php echo __("Score"); ?></span>
                    </div>
                    <div class="col-md-3">
                        <div class="piechart subscriptions"></div>
                    </div>

                    <div class="col-md-3">
                        <h3><?php echo __("You"); ?></h3>
                        <?php if($this->Chart->hasScore($userArtistReviewSnapshot)) : ?>
                            <?php echo (int)($userArtistReviewSnapshot["score"] * 100); ?>%
                        <?php else : ?>
                            N/A
                        <?php endif; ?>
                        <span><?php echo __("Score"); ?></span>
                    </div>
                    <div class="col-md-3">
                        <div class="piechart you"></div>
                    </div>
                <?php else : ?>
                    <?php $login = $this->Html->link(__("Login"), array('controller' => 'users', 'action' => 'login', '?' => array("rurl" => '/artist/view/' . $artist['slug'])), array("class" => "btn btn-primary")); ?>
                    <p><?php echo sprintf(__("%s to see how you and your friends have rated  \"%s\"."), $login, $artist["name"]); ?></p>
                <?php endif; ?>

            </div>
        </div>
    </header>

    <div class="row content">

        <h2><?php echo __("Discography"); ?></h2>
        <?php if(count($albums) > 0) : ?>
            <?php echo $this->element('albumTiledList', array("albums" => $albums)); ?>
        <?php else : ?>
            <div class="loading-wrap" data-post-load="/ajax/getdiscography/<?php echo $artist['slug']; ?>/">
                <i class="fa fa-refresh fa-spin fa-fw"></i>
            </div>
        <?php endif; ?>
    </div>
</article>
<section class="credits">
    <div class="container container-fluid">
        <p>
            <?php echo __("Artist biography and profile image courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>.
            <?php echo __("They were last updated on"); ?> <?php echo date("F j, g:i a", $lastfmArtist["lastsync"]); ?>.
            <?php echo __("User-contributed text is available under the Creative Commons By-SA License and may also be available under the GNU FDL."); ?>
        </p>
    </div>
</section>

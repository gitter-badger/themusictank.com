<nav class="sub-menu">
    <ol class="breadcrumb">
        <li class="active"><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></li>
    </ol>
</nav>

<article class="heading artist-profile">

    <div class="image" <?php if(!is_null($lastfmArtist["image"])) : ?>style="background-image:url(/img/<?php echo $lastfmArtist["image"]; ?>);"<?php endif;?>>
        <div class="fx1"></div>
        <div class="vertical-vignette"></div> 
    </div>

    <aside>
        <h1><?php echo $artist["name"]; ?></h1>
        <section class="biography">
            <?php echo $lastfmArtist["biography"]; ?>
        </section>
    </aside>

    <div class="statistics">
        <section class="tankers">
            <?php echo $this->Chart->getBigPie("track", $artist["slug"], $artistReviewSnapshot); ?>
            <h3><?php echo __("General"); ?></h3>  
            <ul>
                <li class="average"><?php echo $this->Chart->formatScore($artistReviewSnapshot["score_snapshot"]); ?></li>
                <li class="enjoyment"><?php echo $this->Chart->formatPct($artistReviewSnapshot["liking_pct"]); ?><i class="fa fa-smile-o"></i></li>
                <li class="neutral"><?php echo $this->Chart->formatPct($artistReviewSnapshot["neutral_pct"]); ?><i class="fa fa-meh-o"></i></li>
                <li class="displeasure"><?php echo $this->Chart->formatPct($artistReviewSnapshot["disliking_pct"]); ?><i class="fa fa-frown-o"></i></li>
            </ul>  
        </section>

        <?php if(isset($userArtistReviewSnapshot)) : ?>
            <section class="subscribers">
                <h3><?php echo __("Subscriptions"); ?></h3>  
                <?php echo $this->Chart->getBigPie("track", $artist["slug"], $userArtistReviewSnapshot); ?>
                <ul>
                    <li class="average"><?php echo $this->Chart->formatScore($userArtistReviewSnapshot["score_snapshot"]); ?></li>
                    <li class="enjoyment"><?php echo $this->Chart->formatPct($userArtistReviewSnapshot["liking_pct"]); ?><i class="fa fa-smile-o"></i></li>
                    <li class="neutral"><?php echo $this->Chart->formatPct($userArtistReviewSnapshot["neutral_pct"]); ?><i class="fa fa-meh-o"></i></li>
                    <li class="displeasure"><?php echo $this->Chart->formatPct($userArtistReviewSnapshot["disliking_pct"]); ?><i class="fa fa-frown-o"></i></li>
                </ul>
            </section>
        <?php endif; ?>
    </div>

</article>

<section class="discography">
<h2><?php echo __("Discography"); ?></h2>
<?php if(count($albums) > 0) : ?>
    <?php echo $this->element('albumTiledList', array("albums" => $albums)); ?>
<?php else : ?>
    <p><?php echo __("This catalog is not available at the moment."); ?></p>
<?php endif; ?>
</section>

<?php echo $this->Disqus->get('/artists/view/'.$artist["slug"].'/', $artist["name"]); ?>
        
<section class="well well-sm credits">
    <p>
        <?php echo __("Artist biography and profile image courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>. 
        <?php echo __("They were last updated on"); ?> <?php echo date("F j, g:i a", $lastfmArtist["lastsync"]); ?>. 
        <?php echo __("Album information and images courtesy of"); ?> <?php echo $this->Html->link("Rdio.com", "http://www.rdio.com/", array("target" => "_blank")); ?>. 
        <?php echo __("These ones were last updated on"); ?> <?php echo date("F j, g:i a", $rdioArtist["lastsync"]); ?>. User-contributed text is available under the Creative Commons By-SA License and may also be available under the GNU FDL.
    </p>
</section>
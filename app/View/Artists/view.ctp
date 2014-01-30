<article class="artist-profile">
    <header>
        <?php if(!is_null($lastfmArtist["image"])) : ?>
            <?php echo $this->Html->image($lastfmArtist["image"], array("alt" => $artist["name"], "class" => "thumbnail")); ?>
        <?php endif;?>
        <h2><?php echo $artist["name"]; ?></h2>
    </header>

    <div class="artist expandable">
        <div class="wrapper">            
            <?php echo $lastfmArtist["biography"]; ?>
        </div>    
    </div>

    <section class="statistics">
        <h3><?php echo __("Everyone"); ?></h3>
        <p><?php echo __("Average user score"); ?> <?php echo $this->Chart->formatScore($artistReviewSnapshot["score_snapshot"]); ?></p>        
        <p><?php echo __("Enjoyment"); ?> <?php echo $artistReviewSnapshot["liking_pct"]; ?> %</p>
        <p><?php echo __("Disliking"); ?> <?php echo $artistReviewSnapshot["disliking_pct"]; ?> %</p>
        <?php echo $this->Chart->getBigPie("track", $artist["slug"], $artistReviewSnapshot); ?>
    </section>

    <?php if(isset($userArtistReviewSnapshot)) : ?>

    <section class="statistics subscribers">
        <h3><?php echo __("People you are subscribed to"); ?></h3>
        <p><?php echo __("Average subscriber score"); ?> <?php echo $this->Chart->formatScore($userArtistReviewSnapshot["score_snapshot"]); ?></p>        
        <?php $enjoymentTimes =  $this->Chart->getEnjoymentTime($userArtistReviewSnapshot, (int)$track["duration"]); ?>
        <p><?php echo __("Enjoyment"); ?> <?php echo $userArtistReviewSnapshot["liking_pct"]; ?> %</p>
        <p><?php echo __("Disliking"); ?> <?php echo $userArtistReviewSnapshot["disliking_pct"]; ?> %</p>
        <?php echo $this->Chart->getBigPie("track", $artist["slug"], $userArtistReviewSnapshot); ?>
    </section>

    <?php endif; ?>
</article>

<section class="discography">
<?php if(count($albums) > 0) : ?>
    <ul class="tiled-list albums">
    <?php foreach($albums as $album) : ?>
        <li>
            <?php if(!is_null($album["image"])) : ?>
                <?php echo $this->Html->link(
                        $this->Html->image($album["image"], array("alt" => $album["name"], "class" => "thumbnail", "width" => 200, "height" => 200)),
                        array('controller' => 'albums', 'action' => 'view', $album["slug"]),
                        array('escape' => false)
                ); ?>
            <?php endif; ?>

            <p><?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?></p>
            <p><?php echo __("Released"); ?> <?php echo date("F j Y", $album["release_date"]); ?></p>
        </li>
    <?php endforeach; ?>
    </ul>
<?php else : ?>
    <p><?php echo __("This catalog is not available at the moment."); ?></p>
<?php endif; ?>
</section>
        
<p class="credits">
    <?php echo __("Artist biography and profile image courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>. 
    <?php echo __("They were last updated on"); ?> <?php echo date("F j, g:i a", $lastfmArtist["lastsync"]); ?>. 
    <?php echo __("Album information and images courtesy of"); ?> <?php echo $this->Html->link("Rdio.com", "http://www.rdio.com/", array("target" => "_blank")); ?>. 
    <?php echo __("These ones were last updated on"); ?> <?php echo date("F j, g:i a", $rdioArtist["lastsync"]); ?>.
</p>
    
<?php echo $this->Disqus->get('/artists/view/'.$artist["slug"].'/', $artist["name"]); ?>
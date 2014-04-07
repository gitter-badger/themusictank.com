
<nav class="sub-menu">
    <ul class="horizontal">
        <li><?php echo $this->Html->link(__("Artists"), array('controller' => 'artists', 'action' => 'index')); ?></li>
        <li><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></li>
        <li><?php echo $this->Html->link(__("Biography"), array('controller' => 'artists', 'action' => 'view', $artist["slug"], "#" => "biography")); ?></li>
        <li><?php echo $this->Html->link(__("Discography"), array('controller' => 'artists', 'action' => 'view', $artist["slug"], "#" => "discography")); ?></li>
    </ul>
</nav>

<article class="heading artist-profile">

    <div class="thumbnail"
        <?php if(!is_null($lastfmArtist["image"])) : ?>
            style="background-image:url(/img/<?php echo $lastfmArtist["image"]; ?>);"
        <?php endif;?>
    >
        <div class="fx1"></div>
        <div class="fx2"></div> 
    </div>

    <aside class="fixable-hit" id="biography">
        <h1><?php echo $artist["name"]; ?></h1>
        <section class="biography">
            <?php echo $lastfmArtist["biography"]; ?>
        </section>
    </aside>

    <div class="statistics">
        <section class="tankers">
            <h4><?php echo __("Tankers"); ?></h4>            
            <?php echo $this->Chart->getBigPie("track", $artist["slug"], $artistReviewSnapshot); ?>
            <p><?php echo __("Average user score"); ?> <?php echo $this->Chart->formatScore($artistReviewSnapshot["score_snapshot"]); ?></p>        
            <p><?php echo __("Enjoyment"); ?> <?php echo $artistReviewSnapshot["liking_pct"]; ?> %</p>
            <p><?php echo __("Disliking"); ?> <?php echo $artistReviewSnapshot["disliking_pct"]; ?> %</p>
        </section>

        <?php if(isset($userArtistReviewSnapshot)) : ?>
            <section class="subscribers">
                <h3><?php echo __("People you are subscribed to"); ?></h3>
                <p><?php echo __("Average subscriber score"); ?> <?php echo $this->Chart->formatScore($userArtistReviewSnapshot["score_snapshot"]); ?></p>        
                <?php $enjoymentTimes =  $this->Chart->getEnjoymentTime($userArtistReviewSnapshot, (int)$track["duration"]); ?>
                <p><?php echo __("Enjoyment"); ?> <?php echo $userArtistReviewSnapshot["liking_pct"]; ?> %</p>
                <p><?php echo __("Disliking"); ?> <?php echo $userArtistReviewSnapshot["disliking_pct"]; ?> %</p>
                <?php echo $this->Chart->getBigPie("track", $artist["slug"], $userArtistReviewSnapshot); ?>
            </section>
        <?php endif; ?>
    </div>

</article>

<section class="fixable-hit" id="discography">
<h2><?php echo __("Discography"); ?></h2>
<?php if(count($albums) > 0) : ?>
    <ul class="tiled-list albums">
    <?php foreach($albums as $album) : ?>
        <li>
            <a class="thumbnail" href="<?php echo $this->Html->url(array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?>" <?php if(isset($album["image"])){  echo 'style="background-image:url(/img/'.$album["image"].');"'; } ?>>
                &nbsp;
            </a>                
            <time datetime="<?php echo date("c", $album["release_date"]); ?>"><?php echo date("F j Y", $album["release_date"]); ?></time>
            <h3>
                <?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?> 
            </h3>
        </li>
    <?php endforeach; ?>
    </ul>
<?php else : ?>
    <p><?php echo __("This catalog is not available at the moment."); ?></p>
<?php endif; ?>
</section>


<?php echo $this->Disqus->get('/artists/view/'.$artist["slug"].'/', $artist["name"]); ?>

        
<p class="credits">
    <?php echo __("Artist biography and profile image courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>. 
    <?php echo __("They were last updated on"); ?> <?php echo date("F j, g:i a", $lastfmArtist["lastsync"]); ?>. 
    <?php echo __("Album information and images courtesy of"); ?> <?php echo $this->Html->link("Rdio.com", "http://www.rdio.com/", array("target" => "_blank")); ?>. 
    <?php echo __("These ones were last updated on"); ?> <?php echo date("F j, g:i a", $rdioArtist["lastsync"]); ?>. User-contributed text is available under the Creative Commons By-SA License and may also be available under the GNU FDL.
</p>
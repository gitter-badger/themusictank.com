<?php
    $mainPopular = array_shift($popularArtists);
?>


<div class="header-wrapper">
    <?php echo $this->Html->image( $this->App->getImageUrl($mainPopular["LastfmArtist"], "blur"), array("alt" => $mainPopular["Artist"]["name"], "class" => "blurred"));  ?>
    <?php echo $this->Html->image( $this->App->getImageUrl($mainPopular["LastfmArtist"], "big"), array("alt" => $mainPopular["Artist"]["name"], "class" => "clean"));  ?>
    <i class="mask"></i>
</div>

<article class="container container-fluid popular-artists">
    <header class="featured">

        <h1><?php echo $this->Html->link($mainPopular["Artist"]["name"], array('controller' => 'artists', 'action' => 'view', $mainPopular["Artist"]["slug"])); ?></h1>
        <div class="everyone piechart"></div>

        <?php if(count($mainPopular["Albums"])) : ?>
        <section class="row stats tiles albums">
            <?php foreach($mainPopular["Albums"] as $i => $album) : if($i >= 4) break;?>
            <div class="col-xs-3 col-md-3 album">
                <?php echo $this->Html->link($this->Html->image($this->App->getImageUrl($album), array("alt" => $album["name"])), array('controller' => 'albums', 'action' => 'view', $album["slug"]), array('escape' => false, "class" => "thumbnail")); ?>
                <h3><?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?></h3>
                <?php if((int)$album["release_date"] > 0) : ?>
                <time datetime="<?php echo date("c", $album["release_date"]); ?>"><?php echo date("F j Y", $album["release_date"]); ?></time>
                <?php endif; ?>

                <div class="score">
                    <?php if(!is_null($mainPopular["ArtistReviewSnapshot"]["score"])) : ?>
                        <?php echo (int)($mainPopular["ArtistReviewSnapshot"]["score"] * 100); ?>%
                    <?php else : ?>
                        N/A
                    <?php endif; ?>
                    <span><?php echo __("Score"); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </section>
        <?php endif; ?>
    </header>

    <div class="row content">

        <section class="col-xs-12 col-md-12 more-popular-artists">
            <h2><?php echo __('Also popular right now'); ?></h2>
            <?php echo $this->element('artistTiledList', array("artists" => $popularArtists)); ?>
        </section>

        <section class="col-xs-12 col-md-6 search-everything">
            <p class="lead"><?php echo __("Search for an artist, an album title or a track name."); ?></p>
            <form action="/search/" method="get">
                <input class="typeahead" type="text" name="q" value="" placeholder="Search across everything" />
                <input type="submit" />
            </form>
        </section>

        <section class="col-xs-12 col-md-6 browse-by-letter">
            <p class="lead"><?php echo __("Browse our list list of artists alphabetically."); ?></p>
            <?php foreach($artistCategories as $category) : ?>
                <div class="col-xs-1 col-md-2">
                    <?php echo $this->Html->link($category, array('controller' => 'artists', 'action' => 'browse',  strtolower($category))); ?>
                </div>
            <?php endforeach; ?>
        </section>
        <section class="new-album-releases">
            <h2><?php echo __('New album releases'); ?></h2>
            <p class="lead">
            	<?php echo __("Check out the newest additions to our catalog."); ?>
            	<?php echo $this->Html->link(__("View more releases"), array('controller' => 'albums', 'action' => 'newreleases')); ?>
        	</p>
            <?php echo $this->element('albumTiledList', array("albums" => $newReleases)); ?>
        </section>
    </div>

</article>

<script>$(function(){
<?php if(isset($mainPopular["ArtistReviewSnapshot"])) : ?>
tmt.createPie(".everyone.piechart", [{"type" : "smile", "value" : <?php echo $mainPopular["ArtistReviewSnapshot"]["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo $mainPopular["ArtistReviewSnapshot"]["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo $mainPopular["ArtistReviewSnapshot"]["disliking_pct"]; ?>}], {key: "tanker chart-tanker"});
<?php endif; ?>
});</script>

<div class="header-wrapper">
    <div class="cover-image blurred" style="background-image:url(<?php echo $this->App->getImageUrl($lastfmArtist, "blur"); ?>);"></div>
    <div class="cover-image clean" style="background-image:url(<?php echo $this->App->getImageUrl($lastfmArtist, "big"); ?>);"></div>
    <i class="mask"></i>
</div>

<article class="container container-fluid">

    <header>
        <?php echo $this->Html->image( $this->App->getImageUrl($lastfmArtist), array("alt" => $artist["name"], "class" => "thumbnail")); ?>
        <h1><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></h1>
        <div class="everyone piechart"></div>
        <div class="lead"></div>
    </header>

    <div class="content">
    	<small class="report-bug" data-bug-type="artist wiki" data-location="artist/<?php echo $artist["slug"]; ?>" data-user="<?php echo $this->Session->read('Auth.User.User.id'); ?>"><i class="fa fa-bug"></i> <?php echo __("Wrong/weird bio?"); ?></small>

			<p><?php echo $this->StringMaker->composeArtistPresentation($lastfmArtist, $artist); ?></p>

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

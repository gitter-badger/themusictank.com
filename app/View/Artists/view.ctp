
<nav class="sub-menu">
	<div class="container container-fluid">
		<div class="row">
		    <ol class="breadcrumb">
		        <li><?php echo $this->Html->link(__("Artists"), array('controller' => 'artists', 'action' => 'index')); ?></li>
		        <li class="active"><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></li>
		    </ol>
	    </div>
    </div>
</nav>

<section class="jumbotron colored introduction" <?php if(!is_null($lastfmArtist)) : ?>style="background-repeat:no-repeat; background-image:url(<?php echo $this->App->getImageUrl($lastfmArtist, true) ?>);"<?php endif; ?>>
	<div class="container container-fluid" >
		<div class="row">
	        <div class="col-md-8 col-md-offset-4">
           		<h1><?php echo $artist["name"]; ?></h1>
	            <section class="description expandable">
	                <div class="wrapper">
                		<p><?php echo $lastfmArtist["biography"]; ?></p>
						<p><?php // echo $this->StringMaker->composeAlbumPresentation($lastfmAlbum, $album, $artist); ?></p>
					</div>
				</section>
	        </div>
        </div>
    </div>
</section>


<div class="discography odd">
	<div class="container container-fluid">
		<div class="row">
			<div class="col-md-12">
				<h2><?php echo __("Discography"); ?></h2>
			    <?php if(count($albums) > 0) : ?>
			        <?php echo $this->element('albumTiledList', array("albums" => $albums)); ?>
			    <?php else : ?>
			        <div class="loading" data-post-load="/ajax/getdiscography/<?php echo $artist['slug']; ?>/"><?php echo __("Loading..."); ?></div>
			    <?php endif; ?>
			</div>
		</div>
	</div>
</div>

<section class="credits">
    <div class="container container-fluid">
        <p>
            <?php echo __("Artist biography and profile image courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>.
            <?php echo __("They were last updated on"); ?> <?php echo date("F j, g:i a", $lastfmArtist["lastsync"]); ?>.
            User-contributed text is available under the Creative Commons By-SA License and may also be available under the GNU FDL.
        </p>
    </div>
</section>

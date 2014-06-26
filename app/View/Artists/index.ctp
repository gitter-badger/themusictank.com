<nav class="sub-menu">
	<div class="container container-fluid">
		<div class="row">
		    <ol class="breadcrumb">
		        <li class="active"><?php echo $this->Html->link(__("Artists"), array('controller' => 'artists', 'action' => 'index')); ?></li>
		    </ol>
	    </div>
    </div>
</nav>

<div class="header-wrapper">
	<div class="cover-image" style="background-image:url(<?php echo $this->App->getImageUrl($popularArtists[0]["LastfmArtist"], true); ?>);"></div>
	<section class="jumbotron introduction">
		<div class="container container-fluid">
			<div class="row">

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
	        </div>
	    </div>
	</section>
</div>

<div class="container container-fluid">
    <section class="popular-artists">
        <header>
            <h2><?php echo __('Popular right now'); ?></h2>
            <p class="lead"><?php echo __("A list of some of the artists currently popular."); ?></p>
        </header>
        <?php echo $this->element('artistTiledList', array("artists" => $popularArtists)); ?>
    </section>

    <section class="new-album-releases">
        <header>
            <h2><?php echo __('New album releases'); ?></h2>
            <p class="lead">
            	<?php echo __("Check out the newest additions to our catalog."); ?>
            	<?php echo $this->Html->link(__("View more releases"), array('controller' => 'albums', 'action' => 'newreleases')); ?>
        	</p>
        </header>
        <?php echo $this->element('albumTiledList', array("albums" => $newReleases)); ?>
    </section>
</div>



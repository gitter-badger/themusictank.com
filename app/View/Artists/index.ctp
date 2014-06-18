<nav class="sub-menu">
	<div class="container container-fluid">
		<div class="row">
		    <ol class="breadcrumb">
		        <li class="active"><?php echo $this->Html->link(__("Artists"), array('controller' => 'artists', 'action' => 'index')); ?></li>
		    </ol>
	    </div>
    </div>
</nav>

<div class="container container-fluid">

    <div class="row">
        <section class="col-md-12 popular-artists">
            <header>
                <h2><?php echo __('Popular right now'); ?></h2>
                <p class="lead"><?php echo __("A list of popular artists based on listening statistics from Rdio."); ?></p>
            </header>
            <?php echo $this->element('artistTiledList', array("artists" => $popularArtists)); ?>
        </section>
    </div>

    <div class="row colored">
        <section class="col-md-6 search-everything">
            <header>
                <h2><?php echo __("Search"); ?></h2>
                <p class="lead"><?php echo __("Search for an artist, an album title or a track name."); ?></p>
            </header>
            <form action="/search/" method="get">
                <input class="typeahead" type="text" name="q" value="" placeholder="Search across everything" />
                <input type="submit" />
            </form>
        </section>

        <section class="col-md-6 browse-by-letter">
            <header>
                <h2><?php echo __('Browse artists'); ?></h2>
                <p class="lead"><?php echo __("Browse our list list of artists alphabetically."); ?></p>
            </header>
            <?php foreach($artistCategories as $category) : ?>
                <div class="col-xs-1 col-md-2">
                    <?php echo $this->Html->link($category, array('controller' => 'artists', 'action' => 'browse',  strtolower($category))); ?>
                </div>
            <?php endforeach; ?>
        </section>
    </div>

    <div class="row">
        <section class="new-album-releases">
            <header>
                <h2><?php echo __('New album releases'); ?> <span class="label label-info"><?php echo $this->Html->link(__("More"), array('controller' => 'albums', 'action' => 'newreleases')); ?> </span></h2>

                <p class="lead"><?php echo __("Check out the newest additions to our catalog."); ?></p>
            </header>
            <?php echo $this->element('albumTiledList', array("albums" => $newReleases)); ?>
        </section>
    </div>
</div>

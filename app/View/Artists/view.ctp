<div class="container container-fluid">

    <nav class="sub-menu">
        <ol class="breadcrumb">
            <li><?php echo $this->Html->link(__("Artists"), array('controller' => 'artists', 'action' => 'index')); ?></li>
            <li class="active"><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></li>
        </ol>
    </nav>

    <article class="heading artist-profile">

        <div class="image" style="background-image:url(<?php echo $this->App->getImageUrl($lastfmArtist, true) ?>);">
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

            <?php echo $this->element("stats"); ?>
            
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
            
</div>

<section class="credits">
    <div class="container container-fluid">
        <p>
            <?php echo __("Artist biography and profile image courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>. 
            <?php echo __("They were last updated on"); ?> <?php echo date("F j, g:i a", $lastfmArtist["lastsync"]); ?>. 
            <?php echo __("Album information and images courtesy of"); ?> <?php echo $this->Html->link("Rdio.com", "http://www.rdio.com/", array("target" => "_blank")); ?>. 
            <?php echo __("These ones were last updated on"); ?> <?php echo date("F j, g:i a", $rdioArtist["lastsync"]); ?>. User-contributed text is available under the Creative Commons By-SA License and may also be available under the GNU FDL.
        </p>
    </div>
</section>
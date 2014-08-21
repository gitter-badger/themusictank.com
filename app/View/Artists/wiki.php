<?php
    $isLogged = $this->Session->check('Auth.User.User.id');
?>
<div class="header-wrapper">
    <?php echo $this->Html->image( $this->App->getImageUrl($lastfmArtist, "blur"), array("alt" => $artist["name"], "class" => "blurred"));  ?>
    <?php echo $this->Html->image( $this->App->getImageUrl($lastfmArtist, "big"), array("alt" => $artist["name"], "class" => "clean"));  ?>
    <i class="mask"></i>
</div>

<article class="container container-fluid">

    <header class="collapsed">

        <?php $img = $this->Html->image( $this->App->getImageUrl($lastfmArtist), array("alt" => $artist["name"], "class" => "thumbnail")); ?>
        <?php echo $this->Html->link($img, array('controller' => 'albums', 'action' => 'view', $artist["slug"]), array("escape" => false)); ?>

        <h1><?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $artist["slug"])); ?></h1>
    </header>

    <div class="row content headerless">

        <div class="col-md-12">
            <h2><?php echo __("Description"); ?></h2>
            <span class="report-bug" data-bug-type="album wiki" data-location="album/<?php echo $album["slug"]; ?>" data-user="<?php echo $this->Session->read('Auth.User.User.id'); ?>"><i class="fa fa-bug"></i> <?php echo __("Wrong/weird bio?"); ?></span>
            <?php echo $this->StringMaker->composeArtistPresentation($lastfmArtist, $artist); ?>
        </div>
        <div class="col-md-12 lastfm"><a href="http://www.last.fm/"><img src="/img/icon-lastfm.png" alt="Last.fm" title="Last.fm" /></a></div>
    </div>
</article>

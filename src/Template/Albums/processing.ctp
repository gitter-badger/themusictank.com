<?php
    $isLogged = $this->Session->check('Auth.User.User.id');
?>
<div class="header-wrapper">
    <?= $this->Html->image( $album->getImageUrl("blur"), ["alt" => $album->name, "class" => "blurred"]);  ?>
    <?= $this->Html->image( $album->getImageUrl("big"), ["alt" => $album->name, "class" => "clean"]);  ?>
    <i class="mask"></i>
</div>

<article class="container container-fluid">

    <header>
        <div class="picture img-circle">
            <?= $this->Html->link("", ['controller' => 'artists', 'action' => 'view', $album->slug], ['escape' => false, 'style' => sprintf("background-image:url(%s)", $album->getImageUrl())]); ?>
        </div>
        <h1><?= $this->Html->link($album->name, ['controller' => 'albums', 'action' => 'view', $album->slug]); ?></h1>
        <h2><?= $this->Html->link($album->artist->name, ['controller' => 'artists', 'action' => 'view', $album->artist->slug]); ?></h2>
    </header>

    <div class="row content headerless">

        <p class="lead full-width"><?php echo __("Sorry for the inconvenience. The track and album details are currently being processed..."); ?></p>
        <div class="loading-wrap">
            <i class="fa fa-cog fa-spin fa-fw"></i>
        </div>

    </div>
</article>

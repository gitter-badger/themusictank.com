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
        <?= $this->element('headers/albumDetails'); ?>
    </header>

    <div class="row content headerless">

        <p class="lead full-width"><?php echo __("Sorry for the inconvenience. The track and album details are currently being processed..."); ?></p>
        <div class="loading-wrap">
            <i class="fa fa-cog fa-spin fa-fw"></i>
        </div>

    </div>
</article>

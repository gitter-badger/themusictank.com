<?php
    $isLogged = $this->Session->check('Auth.User.User.id');
?>

<?= $this->element('headers/backdrop', ['entity' => $album]); ?>

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

<div class="container container-fluid">
    <section class="row new-album-releases">
        <header>
            <h2><?php echo __("New album releases") ?></h2>
            <p class="lead"><?php echo __("For the week of") . " " . $forTheWeekOf; ?></p>
        </header>        
        <?php echo $this->element('albumTiledList', array("albums" => $newReleases)); ?>
    </section>
</div>
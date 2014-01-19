<?php echo $this->element('chartsmenu'); ?>



<section class="charts">
    <header>
        <h2><?php echo __('Album charts'); ?></h2>
        <h3><?php echo __("Top 100 albums for the week of"); ?> <?php echo $currentWeek; ?></h3>
    </header>
    <ol>
        <?php foreach($albumsChart as $album) : ?>
        <li><?php echo $album["Album"]["name"]; ?> <?php echo $this->Chart->formatScore($album["AlbumReviewSnapshot"]["score_snapshot"]); ?></li>
        <?php endforeach; ?>
    </ol>
</section>
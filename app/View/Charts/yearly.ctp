<?php echo $this->element('chartsmenu'); ?>

<section class="charts">    
    <?php if(isset($albumCharts)) : ?>    
        <header>
            <h2><?php echo __('Album charts'); ?></h2>
            <h3><?php echo __("Top 100 albums for the period"); ?> <?php echo $currentTime; ?></h3>
        </header>
        <ol>
            <?php foreach($albumCharts as $album) : ?>
            <li><?php echo $album["Album"]["name"]; ?> <?php echo $this->Chart->formatScore($album["AlbumReviewSnapshot"]["score_snapshot"]); ?></li>
            <?php endforeach; ?>
        </ol>
    <?php elseif(isset($trackCharts)) : ?>
        <header>
            <h2><?php echo __('Tracks charts'); ?></h2>
            <h3><?php echo __("Top 100 albums for the period"); ?> <?php echo $currentTime; ?></h3>
        </header>
        <ol>
            <?php foreach($trackCharts as $track) : ?>
            <li><?php echo $track["Track"]["title"]; ?> <?php echo $this->Chart->formatScore($track["TrackReviewSnapshot"]["score_snapshot"]); ?></li>
            <?php endforeach; ?>
        </ol>    
    <?php endif; ?>
</section>
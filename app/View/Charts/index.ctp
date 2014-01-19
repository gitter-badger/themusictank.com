<section class="charts">
    <header>
        <h2><?php echo __('Album charts'); ?></h2>
        <p><?php echo __("Top albums for the week of"); ?> <?php echo $currentWeek; ?></p>
    </header>
    <ol>
        <?php foreach($albumsChart as $album) : ?>
        <li><?php echo $album["Album"]["name"]; ?> <?php echo $this->Chart->formatScore($album["AlbumReviewSnapshot"]["score_snapshot"]); ?></li>
        <?php endforeach; ?>
    </ol>    
    <div>
        <?php echo $this->Html->link(__("View complete weekly top 100"), array('controller' => 'charts', 'action' => 'weekly', 'albums', date('Y'), date('W'))); ?>
    </div>
</section>

<section class="charts">
    <header>
        <h2><?php echo __('Track charts'); ?></h2>
        <h3><?php echo __("Top tracks for the week of"); ?> <?php echo $currentWeek; ?></h3>
    </header>
    <ol>
        <?php foreach($tracksChart as $track) : ?>
        <li><?php echo $track["Track"]["title"]; ?> <?php echo $this->Chart->formatScore($track["TrackReviewSnapshot"]["score_snapshot"]); ?></li>
        <?php endforeach; ?>
    </ol>    
    <div>
        <?php echo $this->Html->link(__("View complete weekly top 100"), array('controller' => 'charts', 'action' => 'weekly', 'tracks', date('Y'), date('W'))); ?>
    </div>
</section>
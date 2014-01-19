<ul>
    <li>
        <?php echo $this->Html->link(__("Previous week"), array('controller' => 'charts', 'action' => 'weekly', 'tracks', $previousWeek[0] , $previousWeek[1])); ?>
    </li>
    <li><?php echo $weekStart; ?> <?php echo __("to"); ?> <?php echo $weekEnd; ?></li>
    <li>
        <?php echo $this->Html->link(__("Next week"), array('controller' => 'charts', 'action' => 'weekly', 'tracks', $nextWeek[0] , $nextWeek[1])); ?>
    </li>
</ul>


<section class="charts">    
    <?php if(isset($albumCharts)) : ?>    
        <header>
            <h2><?php echo __('Album charts'); ?></h2>
            <h3><?php echo __("Top 100 albums for the period"); ?> <?php echo $weekStart; ?></h3>
        </header>
        <ol>
            <?php foreach($albumCharts as $album) : ?>
            <li><?php echo $album["Album"]["name"]; ?> <?php echo $this->Chart->formatScore($album["AlbumReviewSnapshot"]["score_snapshot"]); ?></li>
            <?php endforeach; ?>
        </ol>
    <?php elseif(isset($trackCharts)) : ?>
        <header>
            <h2><?php echo __('Tracks charts'); ?></h2>
            <h3><?php echo __("Top 100 albums for the period"); ?> <?php echo $weekStart; ?></h3>
        </header>
        <ol>
            <?php foreach($trackCharts as $track) : ?>
            <li><?php echo $track["Track"]["title"]; ?> <?php echo $this->Chart->formatScore($track["TrackReviewSnapshot"]["score_snapshot"]); ?></li>
            <?php endforeach; ?>
        </ol>    
    <?php endif; ?>
</section>
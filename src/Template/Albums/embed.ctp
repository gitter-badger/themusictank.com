<div class="review-line odd graph">
    <div class="container container-fluid">
        <div class="d3chart big-graph"></div>
    </div>
</div>

<?php $this->start('bottom-extra'); ?>
<?php if($album->lastfm->hasSyncDate()) : ?>
    <script>$(function(){
        var svg = d3.select(".big-graph").append("svg");
        <?php if(isset($album->snapshot)) : ?>
            tmt.createRange(svg, <?php echo json_encode($album->snapshot->ranges); ?>, {key: "everyone range-everyone", total: <?php echo (int)$album->duration; ?>});
            tmt.createLine(svg, <?php echo json_encode($album->snapshot->curve); ?>, {key: "everyone line-everyone", total: <?php echo (int)$album->duration; ?>});
            tmt.createPie(".everyone.piechart", [{"type" : "smile", "value" : <?php echo (int)$album->snapshot->liking_pct; ?>}, {"type" : "meh", "value" : <?php echo (int)$album->snapshot->neutral_pct; ?>}, {"type" : "frown", "value" : <?php echo (int)$album->snapshot->disliking_pct; ?>}], {key: "tanker chart-tanker"});
        <?php endif; ?>
        <?php /* if(isset($album->subscriptionsSnaption)) : ?>
            tmt.createRange(svg, <?php echo json_encode($userAlbumReviewSnapshot["ranges"]); ?>, {key: "user range-user", total: <?php echo (int)$album["duration"]; ?>});
            tmt.createLine(svg, <?php echo json_encode($userAlbumReviewSnapshot["curve"]); ?>, {key: "user line-user", total: <?php echo (int)$album["duration"]; ?>});
            tmt.createPie(".uars.piechart", [{"type" : "smile", "value" : <?php echo (int)$userAlbumReviewSnapshot["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo (int)$userAlbumReviewSnapshot["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo (int)$userAlbumReviewSnapshot["disliking_pct"]; ?>}], {key: "tanker chart-tanker"});
        <?php endif; ?>
        <?php if(isset($profileAlbumReviewSnapshot)) : ?>
            tmt.createRange(svg, <?php echo json_encode($profileAlbumReviewSnapshot["ranges"]); ?>, {key: "profile range-profile", total: <?php echo (int)$album["duration"]; ?>});
            tmt.createLine(svg, <?php echo json_encode($profileAlbumReviewSnapshot["curve"]); ?>, {key: "profile line-profile", total: <?php echo (int)$album["duration"]; ?>});
        <?php endif; */ ?>
    });</script>
<?php endif; ?>
<?php $this->end(); ?>

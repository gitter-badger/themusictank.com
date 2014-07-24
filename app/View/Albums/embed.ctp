<div class="review-line odd graph">
    <div class="container container-fluid">
        <div class="d3chart big-graph"></div>
    </div>
</div>

<script>$(function(){
    var svg = d3.select(".d3chart").append("svg");
    <?php if(isset($albumReviewSnapshot)) : ?>
        tmt.createRange(svg, <?php echo json_encode($albumReviewSnapshot["ranges"]); ?>, {key: "everyone range-everyone", total: <?php echo (int)$album["duration"]; ?>});
        tmt.createLine(svg, <?php echo json_encode($albumReviewSnapshot["curve"]); ?>, {key: "everyone line-everyone", total: <?php echo (int)$album["duration"]; ?>});
        tmt.createPie(".ars.piechart", [{"type" : "smile", "value" : <?php echo $albumReviewSnapshot["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo $albumReviewSnapshot["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo $albumReviewSnapshot["disliking_pct"]; ?>}], {key: "tanker chart-tanker"});
    <?php endif; ?>
    <?php if(isset($userAlbumReviewSnapshot)) : ?>
        tmt.createRange(svg, <?php echo json_encode($userAlbumReviewSnapshot["ranges"]); ?>, {key: "user range-user", total: <?php echo (int)$album["duration"]; ?>});
        tmt.createLine(svg, <?php echo json_encode($userAlbumReviewSnapshot["curve"]); ?>, {key: "user line-user", total: <?php echo (int)$album["duration"]; ?>});
        tmt.createPie(".uars.piechart", [{"type" : "smile", "value" : <?php echo $userAlbumReviewSnapshot["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo $userAlbumReviewSnapshot["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo $userAlbumReviewSnapshot["disliking_pct"]; ?>}], {key: "tanker chart-tanker"});
    <?php endif; ?>
    <?php if(isset($profileAlbumReviewSnapshot)) : ?>
        tmt.createRange(svg, <?php echo json_encode($profileAlbumReviewSnapshot["ranges"]); ?>, {key: "profile range-profile", total: <?php echo (int)$album["duration"]; ?>});
        tmt.createLine(svg, <?php echo json_encode($profileAlbumReviewSnapshot["curve"]); ?>, {key: "profile line-profile", total: <?php echo (int)$album["duration"]; ?>});
    <?php endif; ?>
});</script>

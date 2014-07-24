<div class="review-line odd graph">
    <div class="container container-fluid">
        <div class="d3chart big-graph"></div>
    </div>
</div>

<script>$(function(){
var svg = d3.select(".d3chart").append("svg");

<?php if(isset($trackYoutube)) : ?>
tmt.waveform(svg, <?php echo json_encode($trackYoutube["waveform"]); ?>);
<?php endif; ?>

<?php if(isset($trackReviewSnapshot)) : ?>
tmt.createRange(svg, <?php echo json_encode($trackReviewSnapshot["ranges"]); ?>, {key: "everyone range-everyone", total: <?php echo (int)$track["duration"]; ?>});
tmt.createLine(svg, <?php echo json_encode($trackReviewSnapshot["curve"]); ?>, {key: "everyone line-everyone", total: <?php echo (int)$track["duration"]; ?>});
tmt.createPie(".trs.piechart", [{"type" : "smile", "value" : <?php echo $trackReviewSnapshot["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo $trackReviewSnapshot["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo $trackReviewSnapshot["disliking_pct"]; ?>}], {key: "tanker chart-tanker"});
<?php endif; ?>

<?php if (Hash::check($trackReviewSnapshot, "top") && count($trackReviewSnapshot["top"]) > 1) : ?>
var highgraph = d3.select(".highgraph").append("svg");
tmt.createRange(highgraph, <?php echo json_encode( array_slice($trackReviewSnapshot["ranges"], $trackReviewSnapshot["top"][0], $trackReviewSnapshot["top"][1]) ); ?>, {key: "everyone range-everyone", total: 30});
tmt.createLine(highgraph, <?php echo json_encode( array_slice($trackReviewSnapshot["curve"], $trackReviewSnapshot["top"][0], $trackReviewSnapshot["top"][1])); ?>, {key: "everyone line-everyone", total: 30});
<?php endif; ?>

<?php if (Hash::check($trackReviewSnapshot, "bottom") && count($trackReviewSnapshot["bottom"]) > 1) : ?>
var lowgraph = d3.select(".lowgraph").append("svg");
tmt.createRange(lowgraph, <?php echo json_encode( array_slice($trackReviewSnapshot["ranges"], $trackReviewSnapshot["bottom"][0], $trackReviewSnapshot["bottom"][1]) ); ?>, {key: "everyone range-everyone", total: 30});
tmt.createLine(lowgraph, <?php echo json_encode( array_slice($trackReviewSnapshot["curve"], $trackReviewSnapshot["bottom"][0], $trackReviewSnapshot["bottom"][1]) ); ?>, {key: "everyone line-everyone", total: 30});
<?php endif; ?>

<?php if(isset($userTrackReviewSnapshot)) : ?>
tmt.createRange(svg, <?php echo json_encode($userTrackReviewSnapshot["ranges"]); ?>, {key: "user range-user", total: <?php echo (int)$track["duration"]; ?>});
tmt.createLine(svg, <?php echo json_encode($userTrackReviewSnapshot["curve"]); ?>, {key: "user line-user", total: <?php echo (int)$track["duration"]; ?>});
tmt.createPie(".utrs.piechart", [{"type" : "smile", "value" : <?php echo $userTrackReviewSnapshot["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo $userTrackReviewSnapshot["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo $userTrackReviewSnapshot["disliking_pct"]; ?>}], {key: "tanker chart-tanker"});
<?php endif; ?>

<?php if(isset($subsTrackReviewSnapshot)) : ?>
tmt.createRange(svg, <?php echo json_encode($subsTrackReviewSnapshot["ranges"]); ?>, {key: "user range-user", total: <?php echo (int)$track["duration"]; ?>});
tmt.createLine(svg, <?php echo json_encode($subsTrackReviewSnapshot["curve"]); ?>, {key: "user line-user", total: <?php echo (int)$track["duration"]; ?>});
tmt.createPie(".strs.piechart", [{"type" : "smile", "value" : <?php echo $subsTrackReviewSnapshot["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo $subsTrackReviewSnapshot["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo $subsTrackReviewSnapshot["disliking_pct"]; ?>}], {key: "subs chart-tanker"});
<?php endif; ?>

<?php if(isset($profileTrackReviewSnapshot)) : ?>
tmt.createRange(svg, <?php echo json_encode($profileTrackReviewSnapshot["ranges"]); ?>, {key: "profile range-profile", total: <?php echo (int)$track["duration"]; ?>});
tmt.createLine(svg, <?php echo json_encode($profileTrackReviewSnapshot["curve"]); ?>, {key: "profile line-profile", total: <?php echo (int)$track["duration"]; ?>});
<?php endif; ?>
});</script>

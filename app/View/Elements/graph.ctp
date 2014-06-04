
		<div class="d3chart"></div>



<style>
.axis path, .axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
}

.area {
  fill: lightsteelblue;
}

.range-tankers {
  fill :rgba(22,155,155,0.2);
}

.line {
  fill: none;
  stroke: steelblue;
  stroke-width: 1.5px;
}

.dot {
  fill: white;
  stroke: steelblue;
  stroke-width: 1.5px;
}
</style>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script>
	$(function(){
		var svg = d3.select(".d3chart").append("svg");
		<?php if(isset($trackReviewSnapshot)) : ?>
			tmt.createRange(svg, <?php echo json_encode($trackReviewSnapshot["ranges"]); ?>, {key: "everyone range-everyone", total: <?php echo (int)$track["duration"]; ?>});
			tmt.createLine(svg, <?php echo json_encode($trackReviewSnapshot["curve"]); ?>, {key: "everyone line-everyone", total: <?php echo (int)$track["duration"]; ?>});
		<?php endif; ?>
		<?php if(isset($userTrackReviewSnapshot)) : ?>
			tmt.createRange(svg, <?php echo json_encode($userTrackReviewSnapshot["ranges"]); ?>, {key: "user range-user", total: <?php echo (int)$track["duration"]; ?>});
			tmt.createLine(svg, <?php echo json_encode($userTrackReviewSnapshot["curve"]); ?>, {key: "user line-user", total: <?php echo (int)$track["duration"]; ?>});
		<?php endif; ?>
		<?php if(isset($profileTrackReviewSnapshot)) : ?>
			tmt.createRange(svg, <?php echo json_encode($profileTrackReviewSnapshot["ranges"]); ?>, {key: "profile range-profile", total: <?php echo (int)$track["duration"]; ?>});
			tmt.createLine(svg, <?php echo json_encode($profileTrackReviewSnapshot["curve"]); ?>, {key: "profile line-profile", total: <?php echo (int)$track["duration"]; ?>});
		<?php endif; ?>

	});
</script>

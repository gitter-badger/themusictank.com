<video id="songplayer" class="video-js moo-css" controls preload></video>
<div class="d3chart"></div>

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

        $.getJSON('/ajax/getsong/<?php echo $artist["slug"] . "/" . $track["slug"]; ?>/',
    		function(response) {
    			if(response.feed.entry.length > 0) {
		    		var links = response.feed.entry[0].link;
		    		for (var i = 0, len = links.length; i < len; i++) {
		    			if(links[i].type == "text/html" || links[i].type == "application/x-shockwave-flash") {
		 					videojs('songplayer', { "techOrder": ["youtube"], "src": links[i].href });
		 					return;
		  				}
		  			}
		  		}
  			// fallback to mp3
    	});
	});
</script>

<nav class="sub-menu">
    <ol class="breadcrumb">
        <li><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></li>
        <li><?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?></li>
        <li class="active">"<?php echo $this->Html->link($track["title"], array('controller' => 'tracks', 'action' => 'view', $track["slug"])); ?>"</li>
    </ol>
</nav>

<section class="jumbotron colored introduction">
	<div class="container">
		<div class="row">
			<div class="col-md-2 thumbnail">
	            <?php echo $this->Html->image( $this->App->getImageUrl($album, true), array("alt" => $album["name"])); ?>
	        </div>
	        <div class="col-md-10">
			    <h1><?php echo $track["title"]; ?></h1>
		        <?php if(empty($lastfmTrack["wiki"])) : ?>
		            <p><?php echo sprintf(__("This is track number %s off of %s."), $track["track_num"], $album["name"]); ?></p>
		        <?php else : ?>
		             <?php echo $lastfmTrack["wiki"]; ?>
		        <?php endif; ?>
				<ul>
					<li><?php echo $this->Html->link(__("Review track"), array('controller' => 'player', 'action' => 'play', $track["slug"]), array("class" => "btn btn-primary")); ?></li>
				</ul>
	        </div>
        </div>
    </div>
</section>


<div class="container container-fluid review-details">
	<div class="row">
		<div class="col-md-4">
			<div class="trs piechart"></div>
		</div>
		<div class="col-md-8">
			<p><?php echo $this->StringMaker->composeTrackAppreciation($trackReviewSnapshot, $track, $album, $artist); ?></p>
			<?php
				$pcts = array(
					"enjoyment" => $trackReviewSnapshot["liking_pct"],
					"displeasure" => $trackReviewSnapshot["disliking_pct"],
					"meh" => $trackReviewSnapshot["neutral_pct"]
				);
				asort($pcts, SORT_NUMERIC);
			?>
			<p><?php echo $this->StringMaker->composeTimedAppreciation($pcts, $track); ?></p>
		</div>
	</div>

	<div class="row">
	<?php if(isset($userTrackReviewSnapshot)) : ?>
		<div class="col-md-8">
			<p><?php echo sprintf("You have reviewed '%s' in the past.", $track["title"]); ?></p>
			<p><?php echo $this->Html->link(__("View details"), array('controller' => 'tracks', 'action' => 'by_user', $track["slug"], $this->Session->read('Auth.User.User.slug'))); ?> of your review sessions of <?php echo $track["title"]; ?></p>
		</div>
		<div class="col-md-4">
			<div class="utrs piechart"></div>
		</div>
	<?php else : ?>
		<div class="col-md-12">
			<p>Review '<?php echo $track["title"]; ?>' you wish to see how your opinion compares with others.</p>
			<p><?php echo $this->Html->link(__("Review track"), array('controller' => 'player', 'action' => 'play', $track["slug"]), array("class" => "btn btn-primary")); ?></p>
		</div>
	<?php endif; ?>
	</div>

	<div class="row social">
		<div class="col-md-6">
        <h2><?php echo __("Recent Reviewers"); ?></h2>
			<?php if(count($usersWhoReviewed) > 0) : ?>
                <ul>
                    <?php foreach($usersWhoReviewed as $user) : ?>
                    <li>
                        <?php echo $this->Html->link(
                                array_key_exists("image", $user["User"]) && !is_null($user["User"]["image"]) ?
                                    $this->Html->image($user["User"]["image"], array("alt" => $user["User"]["firstname"] . " " . $user["User"]["lastname"]))
                                    : $user["User"]["firstname"] . " " . $user["User"]["lastname"]
                                ,
                                array('controller' => 'tracks', 'action' => 'by_user', $track["slug"], $user["User"]["slug"]),
                                array("escape" => false)
                        ); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p><?php echo __("Be the first to review this track."); ?></p>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
			<?php if(isset($subsWhoReviewed) && count($subsWhoReviewed > 0)) : ?>
	            <?php if(count($subsWhoReviewed) > 0) : ?>
	                <ul>
	                    <?php foreach($subsWhoReviewed as $idx => $user) : ?>
	                    <li>
	                        <?php
	                        $name = $user["User"]["firstname"] . " " . $user["User"]["lastname"];
	                        echo $this->Html->link(
                                array_key_exists("image", $user["User"]) && !is_null($user["User"]["image"]) ?
                                    $this->Html->image($user["User"]["image"], array("alt" => $name))
                                    : $name
                                ,
                                array('controller' => 'tracks', 'action' => 'by_user', $track["slug"], $user["User"]["slug"]),
                                array("escape" => false)
	                        ); ?>
	                    </li>
	                    <?php if($idx >= 3 && (count($subsWhoReviewed) - 4 > 0)) :  ?>
	                        <li class="others"><?php echo sprintf(__("+ %s others"), count($subsWhoReviewed) - 4); ?></li>
	                    <?php break; endif; ?>
	                    <?php endforeach; ?>
	                </ul>
	                <p>
	                    <?php echo $this->Html->link(sprintf(__("%s of the people you are subscribed to reviewed %s."), count($subsWhoReviewed), $track["title"]),
	                        array('controller' => 'tracks', 'action' => 'by_subscriptions', $track["slug"]));
	                    ?>
	                </p>
	            <?php else : ?>
	                <p><?php echo __("None of your subscriptions have reviewed this track."); ?></p>
	            <?php endif; ?>
	        <?php endif; ?>
        </div>
	</div>

	<div class="row">
		<div class="col-md-12">
            <p>
	            <?php echo sprintf(__("This is track number %s off of %s."), $track["track_num"], $album["name"]); ?>
	            <?php if(isset($previousTrack)) : ?>
	                <?php echo sprintf("It is preceded by %s.", $this->Html->link($previousTrack["title"], array('controller' => 'tracks', 'action' => 'view', $previousTrack["slug"]))); ?>
	            <?php endif; ?>
				<?php if(isset($nextTrack)) : ?>
					<?php echo sprintf("It is followed by %s.", $this->Html->link($nextTrack["title"], array('controller' => 'tracks', 'action' => 'view', $nextTrack["slug"]))); ?>
	            <?php endif; ?>
            </p>
   		</div>
	</div>

</div>

<div class="graph">
	<video id="songplayer" class="video-js moo-css" controls ></video>
	<div class="d3chart"></div>
</div>

<section class="credits">
    <div class="container container-fluid">
        <p>
            <?php echo __("Track description courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>.
            <?php echo __("It was last updated on"); ?> <?php echo date("F j, g:i a", $lastfmTrack["lastsync"]); ?>.
        </p>
    </div>
</section>

<script>$(function(){
	var svg = d3.select(".d3chart").append("svg");
	<?php if(isset($trackReviewSnapshot)) : ?>
		tmt.createRange(svg, <?php echo json_encode($trackReviewSnapshot["ranges"]); ?>, {key: "everyone range-everyone", total: <?php echo (int)$track["duration"]; ?>});
		tmt.createLine(svg, <?php echo json_encode($trackReviewSnapshot["curve"]); ?>, {key: "everyone line-everyone", total: <?php echo (int)$track["duration"]; ?>});
		tmt.createPie(".trs.piechart", [{"type" : "smile", "value" : <?php echo $trackReviewSnapshot["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo $trackReviewSnapshot["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo $trackReviewSnapshot["disliking_pct"]; ?>}], {key: "tanker chart-tanker"});
	<?php endif; ?>
	<?php if(isset($userTrackReviewSnapshot)) : ?>
		tmt.createRange(svg, <?php echo json_encode($userTrackReviewSnapshot["ranges"]); ?>, {key: "user range-user", total: <?php echo (int)$track["duration"]; ?>});
		tmt.createLine(svg, <?php echo json_encode($userTrackReviewSnapshot["curve"]); ?>, {key: "user line-user", total: <?php echo (int)$track["duration"]; ?>});
		tmt.createPie(".utrs.piechart", [{"type" : "smile", "value" : <?php echo $userTrackReviewSnapshot["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo $userTrackReviewSnapshot["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo $userTrackReviewSnapshot["disliking_pct"]; ?>}], {key: "tanker chart-tanker"});
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
});</script>

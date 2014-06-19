<nav class="sub-menu">
	<div class="container container-fluid">
		<div class="row">
		    <ol class="breadcrumb">
		        <li><?php echo $this->Html->link(__("Artists"), array('controller' => 'artists', 'action' => 'index')); ?></li>
		        <li><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></li>
		        <li><?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?></li>
		        <li class="active"><?php echo $this->Html->link($track["title"], array('controller' => 'tracks', 'action' => 'view', $track["slug"])); ?></li>
		    </ol>
	    </div>
    </div>
</nav>

<div class="header-wrapper introduction">
	<?php if(!is_null($album["image"])) : ?>
		<div class="cover-image" style="background-image:url(<?php echo $this->App->getImageUrl($album, true); ?>);"></div>
	<?php endif; ?>
	<section class="jumbotron">
		<div class="container container-fluid">
			<div class="row">
				<div class="col-xs-12 col-sm-6 col-md-3 thumbnail">
		            <?php echo $this->Html->image( $this->App->getImageUrl($album, true), array("alt" => $album["name"])); ?>
		        </div>
		        <div class="col-xs-12 col-sm-6 col-md-8 col-md-offset-1">
		        	<?php if(!is_null($trackReviewSnapshot["score"])) : ?>
						<div class="score">
							<?php echo (int)($trackReviewSnapshot["score"] * 100); ?>%
						</div>
					<?php endif; ?>

				    <h1><?php echo $track["title"]; ?></h1>

		            <section class="description expandable">
		                <div class="wrapper">
							<p><?php echo $this->StringMaker->composeTrackPresentation($lastfmTrack, $track, $album, $artist); ?></p>
						</div>
					</section>
					<ul>
						<li><?php echo $this->Html->link(__("Review track"), array('controller' => 'player', 'action' => 'play', $track["slug"]), array("class" => "btn btn-primary")); ?></li>
					</ul>
		        </div>
	        </div>
	    </div>
	</section>
</div>

<div class="review-line appreciation odd">
	<div class="container container-fluid">
		<div class="row royalSlider">
			<section class="col-md-12">
				<div class="rsABlock trs piechart" data-move-effect="left"></div>
				<p class="rsABlock" data-move-effect="right"><?php echo $this->StringMaker->composeTrackAppreciation($trackReviewSnapshot, $track, $album, $artist); ?></p>
				<p class="rsABlock" data-move-effect="right"><?php echo $this->StringMaker->composeTimedAppreciation($trackReviewSnapshot, $track["duration"]); ?></p>
        		<div class="rsCaption"><?php echo __("Everyone"); ?></div>
			</section>

			<?php if(isset($subsTrackReviewSnapshot)) : ?>
			<section class="col-md-12">
				<?php if(!count($subsTrackReviewSnapshot)) : ?>
					<p><?php echo sprintf("Your subscribers have not reviewed %s yet.", $track['title']); ?></p>
				<?php else : ?>
					<div class="rsABlock strs piechart" data-move-effect="left"></div>
					<p class="rsABlock" data-move-effect="right"><?php echo $this->StringMaker->composeTrackAppreciation($subsTrackReviewSnapshot, $track, $album, $artist); ?></p>
					<p dclass="rsABlock" ata-move-effect="right"><?php echo $this->StringMaker->composeTimedAppreciation($subsTrackReviewSnapshot, $track["duration"]); ?></p>
        			<div class="rsCaption"><?php echo __("People you are subscribed to"); ?></div>
				<?php endif; ?>
			</section>
			<?php endif; ?>

			<?php if(isset($userTrackReviewSnapshot)) : ?>
			<section class="col-md-12">
				<?php if(!count($userTrackReviewSnapshot)) : ?>
					<p>Review '<?php echo $track["title"]; ?>' you wish to see how your opinion compares with others.</p>
					<p><?php echo $this->Html->link(__("Review track"), array('controller' => 'player', 'action' => 'play', $track["slug"]), array("class" => "btn btn-primary")); ?></p>
				<?php else : ?>
					<div class="rsABlock utrs piechart" data-move-effect="left"></div>
					<p class="rsABlock" data-move-effect="right"><?php echo $this->StringMaker->composeTrackAppreciation($userTrackReviewSnapshot, $track, $album, $artist); ?></p>
					<p class="rsABlock" data-move-effect="right"><?php echo $this->StringMaker->composeTimedAppreciation($userTrackReviewSnapshot, $track["duration"]); ?></p>
        			<div class="rsCaption"><?php echo __("You"); ?></div>
				<?php endif; ?>
			</section>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php if(count($trackReviewSnapshot["top"]) > 1 && count($trackReviewSnapshot["bottom"] > 1)) : ?>
<div class="review-line highlights even">
	<div class="container container-fluid">
		<div class="row">
			<div class="col-md-12">

			<p><?php echo $this->StringMaker->composeTrackHighlights($trackReviewSnapshot); ?></p>
				<div class="col-md-6 highlight">
					<div class="highgraph" style="height:200px;"></div>
					<button type="button">Play</button>
				</div>
				<div class="col-md-6 lowlight">
					<div class="lowgraph" style="height:200px;"></div>
					<button type="button">Play</button>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<div class="review-line odd social">
	<div class="container container-fluid">
		<div class="row">
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

<div class="review-line even graph">
	<div class="container container-fluid">
		<video id="songplayer" class="video-js moo-css" controls data-song="<?php echo $artist["slug"]?>/<?php echo $track["slug"]; ?>/"></video>
		<div class="d3chart" style="height:500px;"></div>
	</div>
</div>

<section class="credits">
    <div class="container container-fluid">
        <p>
            <?php echo __("Track description courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>.
            <?php echo __("It was last updated on"); ?> <?php echo date("F j, g:i a", $lastfmTrack["lastsync"]); ?>.
            User-contributed text is available under the Creative Commons By-SA License and may also be available under the GNU FDL.
        </p>
    </div>
</section>

<script>$(function(){
var svg = d3.select(".d3chart").append("svg");
<?php if(isset($trackReviewSnapshot)) : ?>
tmt.createRange(svg, <?php echo json_encode($trackReviewSnapshot["ranges"]); ?>, {key: "everyone range-everyone", total: <?php echo (int)$track["duration"]; ?>});
tmt.createLine(svg, <?php echo json_encode($trackReviewSnapshot["curve"]); ?>, {key: "everyone line-everyone", total: <?php echo (int)$track["duration"]; ?>});
tmt.createPie(".trs.piechart", [{"type" : "smile", "value" : <?php echo $trackReviewSnapshot["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo $trackReviewSnapshot["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo $trackReviewSnapshot["disliking_pct"]; ?>}], {key: "tanker chart-tanker"});
<?php /*
var highgraph = d3.select(".highgraph").append("svg");
tmt.createRange(highgraph, <?php echo json_encode( array_slice($trackReviewSnapshot["ranges"], $trackReviewSnapshot["top"][0], $trackReviewSnapshot["top"][1]) ); ?>, {key: "everyone range-everyone", total: 30});
tmt.createLine(highgraph, <?php echo json_encode( array_slice($trackReviewSnapshot["curve"], $trackReviewSnapshot["top"][0], $trackReviewSnapshot["top"][1])); ?>, {key: "everyone line-everyone", total: 30});

var lowgraph = d3.select(".lowgraph").append("svg");
tmt.createRange(lowgraph, <?php echo json_encode( array_slice($trackReviewSnapshot["ranges"], $trackReviewSnapshot["bottom"][0], $trackReviewSnapshot["bottom"][1]) ); ?>, {key: "everyone range-everyone", total: 30});
tmt.createLine(lowgraph, <?php echo json_encode( array_slice($trackReviewSnapshot["curve"], $trackReviewSnapshot["bottom"][0], $trackReviewSnapshot["bottom"][1]) ); ?>, {key: "everyone line-everyone", total: 30});
*/ ?>
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

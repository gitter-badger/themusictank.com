
<div class="header-wrapper">
	<?php if(!is_null($album["image"])) : ?>
		<div class="cover-image" style="background-image:url(<?php echo $this->App->getImageUrl($album, "big"); ?>);"></div>
	<?php endif; ?>
	<section class="jumbotron introduction">
		<div class="container container-fluid">
			<div class="row">
				<div class="col-xs-12 col-sm-6 col-md-3 mugshot">
		            <?php echo $this->Html->image( $this->App->getImageUrl($album), array("alt" => $album["name"], "class" => "thumbnail")); ?>
		        </div>
		        <div class="col-xs-12 col-sm-6 col-md-9 description">
		        	<?php if(!is_null($albumReviewSnapshot["score"])) : ?>
						<div class="score">
							<?php echo (int)($albumReviewSnapshot["score"] * 100); ?>%
						</div>
					<?php endif; ?>
	           		<h1><?php echo $album["name"]; ?></h1>
		            <section class="description expandable">
		                <div class="wrapper">
							<p><?php echo $this->StringMaker->composeAlbumPresentation($lastfmAlbum, $album, $artist); ?></p>
						</div>
					</section>
		        </div>
	        </div>
	    </div>
	</section>
</div>

<nav class="sub-menu">
	<div class="container container-fluid">
		<div class="row">
		    <ol class="breadcrumb">
		        <li><?php echo $this->Html->link(__("Artists"), array('controller' => 'artists', 'action' => 'index')); ?></li>
		        <li><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></li>
		        <li class="active"><?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?></li>
		    </ol>
	    </div>
    </div>
</nav>

<?php if ((int)$lastfmAlbum["lastsync"] > 0) : ?>

	<div class="review-line appreciation odd">
		<div class="container container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?php $album["duration"] = 260*12; ?>
					<div class="ars piechart"></div>
					<p><?php echo $this->StringMaker->composeAlbumAppreciation($albumReviewSnapshot, $album, $artist); ?></p>
					<p><?php echo $this->StringMaker->composeTimedAppreciation($albumReviewSnapshot, $album["duration"]); ?></p>
				</div>
			</div>
		</div>
	</div>

	<div class="review-line even social">
		<div class="container container-fluid">
			<div class="row">
				<div class="col-md-6">
		        <h2><?php echo __("Recent Reviewers"); ?></h2>
					 <?php if(count($usersWhoReviewed) > 0) : ?>
		                <ul>
		                    <?php foreach($usersWhoReviewed as $user) : ?>
		                    <li>
		                    	<img src="<?php echo $this->App->getImageUrl($user["User"], true); ?>" alt="<?php $user["User"]["firstname"] . " " . $user["User"]["lastname"]; ?>" />
		                    </li>
		                    <?php endforeach; ?>
		                </ul>
		            <?php else : ?>
		                <p><?php echo __("Be the first to review a track off this album."); ?></p>
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
	                                        array('controller' => 'tracks', 'action' => 'by_user', $album["slug"], $user["User"]["slug"]),
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
	                        <p><?php echo __("None of your subscriptions have reviewed a track on this album."); ?></p>
	                    <?php endif; ?>
	                <?php endif; ?>
		        </div>
			</div>
		</div>
	</div>

	<div class="review-line odd graph">
		<div class="container container-fluid">
			<div class="d3chart big-graph"></div>
		</div>
	</div>

	<div class="review-line odd tracklisting">
		<div class="container container-fluid">
			<h3><?php echo __("Tracks"); ?></h3>
			<?php if(count($tracks)) : ?>
				<ol>
				<?php foreach ($tracks as $track) : ?>
					<li>
						<?php echo $this->Html->link($track["title"], array('controller' => 'tracks', 'action' => 'view', $track["slug"])); ?>
					</li>
				<?php endforeach; ?>
				</ol>
			<?php else : ?>
				<p><?php echo __("We could not fetch the album's tracks for the moment. We we try again every 2 hours until we can have an answer."); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<section class="credits">
	    <div class="container container-fluid">
	        <p>
	            <?php echo __("Album description courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>.
	            <?php echo __("They were last updated on"); ?> <?php echo date("F j, g:i a", $lastfmAlbum["lastsync"]); ?>.
	            <?php echo __("User-contributed text is available under the Creative Commons By-SA License and may also be available under the GNU FDL."); ?>
	        </p>
	    </div>
	</section>

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

<?php else : ?>

	<div class="review-line odd tracklisting">
		<div class="container container-fluid">
			<p><?php echo __("Sorry for the inconvenience but the track and album details are currently being processed. They should be available in less than 2 hours."); ?></p>
	        <div class="loading-wrap">
	        	<i class="fa fa-refresh fa-spin fa-fw"></i>
	        </div>
		</div>
	</div>

<?php endif; ?>

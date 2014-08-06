<?php
    $isLogged = $this->Session->check('Auth.User.User.id');
?>
<div class="header-wrapper">
    <?php echo $this->Html->image( $this->App->getImageUrl($album, "blur"), array("alt" => $album["name"], "class" => "blurred"));  ?>
    <?php echo $this->Html->image( $this->App->getImageUrl($album, "big"), array("alt" => $album["name"], "class" => "clean"));  ?>
    <i class="mask"></i>
</div>

<article class="container container-fluid">

    <header>

        <?php $img = $this->Html->image( $this->App->getImageUrl($album), array("alt" => $album["name"], "class" => "thumbnail")); ?>
        <?php echo $this->Html->link($img, array('controller' => 'albums', 'action' => 'view', $album["slug"]), array("escape" => false)); ?>

        <h1><?php echo $this->Html->link($track["title"], array('controller' => 'tracks', 'action' => 'view', $track["slug"])); ?></h1>

        <h2><?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?> &dash; <?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></h2>

        <?php if((int)$album["release_date"] > 0) : ?>
            <time datetime="<?php echo date("c", $album["release_date"]); ?>"><?php echo date("F j Y", $album["release_date"]); ?></time>
        <?php endif; ?>

        <div class="score">
            <?php if(!is_null($trackReviewSnapshot["score"])) : ?>
                <?php echo (int)($trackReviewSnapshot["score"] * 100); ?>%
            <?php else : ?>
                N/A
            <?php endif; ?>
            <span><?php echo __("Score"); ?></span>
        </div>

        <div class="everyone piechart"></div>

		<div class="row stats">

            <div class="col-md-2 enjoyment">
            	<span><?php echo __("Enjoyed"); ?></span>
            	<em>
            		<?php if($this->Chart->isNotAvailable($trackReviewSnapshot)) : ?>
            			N/A
            		<?php else : ?>
            			<?php echo (int)$trackReviewSnapshot["liking_pct"]; ?>%
            		<?php endif; ?>
            	</em>
        	</div>
            <div class="col-md-2 dislike">
            	<span><?php echo __("Disliked"); ?></span>
            	<em>
            		<?php if($this->Chart->isNotAvailable($trackReviewSnapshot)) : ?>
            			N/A
            		<?php else : ?>
            			<?php echo (int)$trackReviewSnapshot["disliking_pct"]; ?>%
            		<?php endif; ?>
            	</em>
        	</div>

        	<?php if(isset($bestArea)) : ?>
            <div class="col-md-4 best best-part">
            	<span><?php echo __("Best area"); ?></span>
        		--
            </div>
    		<?php endif; ?>

        	<?php if(isset($worstArea)) : ?>
            <div class="col-md-4 worst worst-area">
            	<span><?php echo __("Worst area"); ?></span>
        		--
            </div>
    		<?php endif; ?>

    		<div class="col-md-12 social">

		        <?php if($isLogged) : ?>
		        	<div class="col-md-3">
			        	<h3><?php echo __("Your subscription"); ?></h3>
					    <?php if(isset($userTrackReviewSnapshot) && !is_null($userTrackReviewSnapshot["score"])) : ?>
			                <?php echo (int)($userTrackReviewSnapshot["score"] * 100); ?>%
			            <?php else : ?>
			                N/A
			            <?php endif; ?>
			            <span><?php echo __("Score"); ?></span>
		            </div>
		            <div class="col-md-3">
		            	<div class="piechart subscriptions"></div>
				    </div>

				    <div class="col-md-3">
			        	<h3><?php echo __("You"); ?></h3>
			        	<?php if(isset($profileTrackReviewSnapshot) && !is_null($profileTrackReviewSnapshot["score"])) : ?>
			                <?php echo (int)($profileTrackReviewSnapshot["score"] * 100); ?>%
			            <?php else : ?>
			                N/A
			            <?php endif; ?>
			            <span><?php echo __("Score"); ?></span>
				    </div>
		            <div class="col-md-3">
		            	<div class="piechart you"></div>
				    </div>
		    	<?php else : ?>
					<?php $login = $this->Html->link(__("Login"), array('controller' => 'users', 'action' => 'login', '?' => array("rurl" => '/albums/view/' . $track['slug'])), array("class" => "btn btn-primary")); ?>
					<p><?php echo sprintf(__("%s to see how you and your friends have rated  \"%s\"."), $login, $track["title"]); ?></p>
		    	<?php endif; ?>

    		</div>
        </div>
    </header>

	<?php $wiki = $this->StringMaker->composeTrackPresentation($lastfmTrack, $track, $album, $artist); ?>
	<?php if(strlen($wiki) > 0) : ?>
	<div class="row wiki <?php echo strlen($wiki) <= 800 ? "full" : ""; ?>">
		<div class="col-md-12 lead">
			<?php echo substr($wiki, 0, 800); ?></p>
        	<i class="mask"></i>
		</div>
		<div class="col-md-4 lastfm"><a href="http://www.last.fm/"><img src="/img/icon-lastfm.png" alt="Last.fm" title="Last.fm" /></a></div>
        <div class="col-md-4 bug"><span class="report-bug" data-bug-type="track wiki" data-location="album/<?php echo $album["slug"]; ?>" data-user="<?php echo $this->Session->read('Auth.User.User.id'); ?>"><i class="fa fa-bug"></i> <?php echo __("Wrong/weird bio?"); ?></span></div>
        <div class="col-md-4 readmore">
        	<?php if(strlen($wiki) > 800) : ?>
            	<?php echo $this->Html->link("Read more", array('controller' => 'track', 'action' => 'wiki', $album["slug"]), array("class" => "btn btn-primary")); ?>
        	<?php endif; ?>
        </div>
    </div>
	<?php endif; ?>


    <div class="row content">

		<div class="big-graph"></div>

		<div class="streamer" <?php echo $this->App->getTrackPlayerAttributes($artist, $track, $trackYoutube); ?>>

			<div class="progress-wrap">
				<div class="progress">
				  <div class="progress-bar loaded-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="<?php echo $track["duration"]; ?>" style="width: 0%;"></div>
				  <div class="progress-bar playing-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="<?php echo $track["duration"]; ?>" style="width: 0%;"></div>
				</div>
			   <div class="cursor"></div>
		   </div>

			<div class="position">0</div>
			<i class="play fa fa-stop"></i>
			<div class="duration"></div>

			<small class="report-bug" data-bug-type="track player" data-location="artist/<?php echo $artist["slug"]; ?>|album/<?php echo $album["slug"]; ?>|track<?php echo $track["slug"]; ?>" data-user="<?php echo $this->Session->read('Auth.User.User.id'); ?>"><i class="fa fa-bug"></i> <?php echo __("Wrong song?"); ?></small>
		</div>


			<?php if($isLogged) : ?>
				<?php if(isset($subsTrackReviewSnapshot)) : ?>
				<section class="col-md-12">
					<?php if(!count($subsTrackReviewSnapshot)) : ?>
						<p><?php echo sprintf("Your subscribers have not reviewed %s yet.", $track['title']); ?></p>
					<?php else : ?>
						<div class="rsABlock strs piechart" data-move-effect="left"></div>
						<p class="rsABlock" data-move-effect="right"><?php echo $this->StringMaker->composeTrackAppreciation($subsTrackReviewSnapshot, $track, $album, $artist); ?></p>
						<p dclass="rsABlock" data-move-effect="right"><?php echo $this->StringMaker->composeTimedAppreciation($subsTrackReviewSnapshot, $track["duration"]); ?></p>
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
			<?php else : ?>
				<section class="col-md-12">
					<?php $login = $this->Html->link(__("Login"), array('controller' => 'users', 'action' => 'login', '?' => array("rurl" => '/tracks/view/' . $track['slug']))); ?>
					<p class="rsABlock" data-move-effect="right"><?php echo sprintf(__("%s to see how you have rated %s"), $login, $track["title"]); ?></p>
        			<div class="rsCaption"><?php echo __("You"); ?></div>
				</section>
			<?php endif; ?>

			<?php if(array_key_exists("top", $trackReviewSnapshot)) : ?>
				<?php if(count($trackReviewSnapshot["top"]) > 1 && count($trackReviewSnapshot["bottom"] > 1)) : ?>
					<div class="row">
						<div class="col-md-6 highlight">
							<p><?php echo $this->StringMaker->composeTrackHighlight($trackReviewSnapshot); ?></p>
							<div class="highgraph"></div>
							<button type="button" data-from="<?php echo $trackReviewSnapshot["top"][0]; ?>" data-to="<?php echo $trackReviewSnapshot["top"][1]; ?>">Play</button>
						</div>
						<div class="col-md-6 lowlight">
							<p><?php echo $this->StringMaker->composeTrackLowpoint($trackReviewSnapshot); ?></p>
							<div class="lowgraph"></div>
							<button type="button" data-from="<?php echo $trackReviewSnapshot["bottom"][0]; ?>" data-to="<?php echo $trackReviewSnapshot["bottom"][1]; ?>">Play</button>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>

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

</article>

<section class="credits">
    <div class="container container-fluid">
        <p>
            <?php echo __("Track description courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>.
            <?php echo __("It was last updated on"); ?> <?php echo date("F j, g:i a", $lastfmTrack["lastsync"]); ?>.
            <?php echo __("User-contributed text is available under the Creative Commons By-SA License and may also be available under the GNU FDL."); ?>
        </p>
    </div>
</section>




<script>$(function(){
var svg = d3.select(".big-graph").append("svg");

<?php if(isset($trackYoutube)) : ?>
tmt.waveform(svg, <?php echo json_encode($trackYoutube["waveform"]); ?>, {key: "waveform", total: <?php echo (int)$track["duration"]; ?>});
<?php endif; ?>

<?php if(isset($trackReviewSnapshot)) : ?>
tmt.createRange(svg, <?php echo json_encode($trackReviewSnapshot["ranges"]); ?>, {key: "everyone range-everyone", total: <?php echo (int)$track["duration"]; ?>});
tmt.createLine(svg, <?php echo json_encode($trackReviewSnapshot["curve"]); ?>, {key: "everyone line-everyone", total: <?php echo (int)$track["duration"]; ?>});
tmt.createPie(".everyone.piechart", [{"type" : "smile", "value" : <?php echo $trackReviewSnapshot["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo $trackReviewSnapshot["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo $trackReviewSnapshot["disliking_pct"]; ?>}], {key: "tanker chart-tanker"});
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

<?php if(isset($previousTrack)) : ?>
tmt.createPie(".prevtrack.piechart", [{"type" : "smile", "value" : <?php echo (int)$previousTrack["TrackReviewSnapshot"]["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo (int)$previousTrack["TrackReviewSnapshot"]["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo (int)$previousTrack["TrackReviewSnapshot"]["disliking_pct"]; ?>}], {key: "prev chart-tanker"});
<?php endif; ?>

<?php if(isset($nextTrack)) : ?>
tmt.createPie(".nexttrack.piechart", [{"type" : "smile", "value" : <?php echo (int)$nextTrack["TrackReviewSnapshot"]["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo (int)$nextTrack["TrackReviewSnapshot"]["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo (int)$nextTrack["TrackReviewSnapshot"]["disliking_pct"]; ?>}], {key: "next chart-tanker"});
<?php endif; ?>
});</script>

<?php /*


<div class="header-wrapper introduction">
	<?php if(!is_null($album["image"])) : ?>
		<div class="cover-image" style="background-image:url(<?php echo $this->App->getImageUrl($album, "big"); ?>);"></div>
	<?php endif; ?>
	<section class="jumbotron">
		<div class="container container-fluid">
			<div class="row">
				<div class="col-xs-12 col-sm-6 col-md-3 mugshot">
		            <?php echo $this->Html->image( $this->App->getImageUrl($album), array("alt" => $album["name"], "class" => "thumbnail")); ?>
		        </div>
		        <div class="col-xs-12 col-sm-6 col-md-8 col-md-offset-1 description">
		        	<?php if(!is_null($trackReviewSnapshot["score"])) : ?>
						<div class="score">
							<?php echo (int)($trackReviewSnapshot["score"] * 100); ?>%
						</div>
					<?php endif; ?>

				    <h1><?php echo $track["title"]; ?></h1>
		            <section>
						<p><?php echo $this->StringMaker->composeTrackPresentation($lastfmTrack, $track, $album, $artist); ?></p>
					</section>
					<small class="report-bug" data-bug-type="track wiki" data-location="artist/<?php echo $artist["slug"]; ?>|album/<?php echo $album["slug"]; ?>|track<?php echo $track["slug"]; ?>" data-user="<?php echo $this->Session->read('Auth.User.User.id'); ?>"><i class="fa fa-bug"></i> <?php echo __("Wrong/weird bio?"); ?></small>
					<ul>
						<li><?php echo $this->Html->link(__("Review track"), array('controller' => 'player', 'action' => 'play', $track["slug"]), array("class" => "btn btn-primary")); ?></li>
					</ul>
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
		        <li><?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?></li>
		        <li class="active"><?php echo $this->Html->link($track["title"], array('controller' => 'tracks', 'action' => 'view', $track["slug"])); ?></li>
		    </ol>
	    </div>
    </div>
</nav>

<div class="review-line appreciation odd">
	<div class="container container-fluid">
		<div class="row royalSlider">
			<section class="col-md-12">
				<div class="rsABlock trs piechart" data-move-effect="left"></div>
				<p class="rsABlock" data-move-effect="right"><?php echo $this->StringMaker->composeTrackAppreciation($trackReviewSnapshot, $track, $album, $artist); ?></p>
				<p class="rsABlock" data-move-effect="right"><?php echo $this->StringMaker->composeTimedAppreciation($trackReviewSnapshot, $track["duration"]); ?></p>
        		<div class="rsCaption"><?php echo __("Everyone"); ?></div>
			</section>

			<?php if($isLogged) : ?>
				<?php if(isset($subsTrackReviewSnapshot)) : ?>
				<section class="col-md-12">
					<?php if(!count($subsTrackReviewSnapshot)) : ?>
						<p><?php echo sprintf("Your subscribers have not reviewed %s yet.", $track['title']); ?></p>
					<?php else : ?>
						<div class="rsABlock strs piechart" data-move-effect="left"></div>
						<p class="rsABlock" data-move-effect="right"><?php echo $this->StringMaker->composeTrackAppreciation($subsTrackReviewSnapshot, $track, $album, $artist); ?></p>
						<p dclass="rsABlock" data-move-effect="right"><?php echo $this->StringMaker->composeTimedAppreciation($subsTrackReviewSnapshot, $track["duration"]); ?></p>
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
			<?php else : ?>
				<section class="col-md-12">
					<?php $login = $this->Html->link(__("Login"), array('controller' => 'users', 'action' => 'login', '?' => array("rurl" => '/tracks/view/' . $track['slug']))); ?>
					<p class="rsABlock" data-move-effect="right"><?php echo sprintf(__("%s to see how you have rated %s"), $login, $track["title"]); ?></p>
        			<div class="rsCaption"><?php echo __("You"); ?></div>
				</section>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php if(array_key_exists("top", $trackReviewSnapshot)) : ?>
	<?php if(count($trackReviewSnapshot["top"]) > 1 && count($trackReviewSnapshot["bottom"] > 1)) : ?>
	<div class="review-line highlights even">
		<div class="container container-fluid">
			<div class="row">
				<div class="col-md-6 highlight">
					<p><?php echo $this->StringMaker->composeTrackHighlight($trackReviewSnapshot); ?></p>
					<div class="highgraph"></div>
					<button type="button" data-from="<?php echo $trackReviewSnapshot["top"][0]; ?>" data-to="<?php echo $trackReviewSnapshot["top"][1]; ?>">Play</button>
				</div>
				<div class="col-md-6 lowlight">
					<p><?php echo $this->StringMaker->composeTrackLowpoint($trackReviewSnapshot); ?></p>
					<div class="lowgraph"></div>
					<button type="button" data-from="<?php echo $trackReviewSnapshot["bottom"][0]; ?>" data-to="<?php echo $trackReviewSnapshot["bottom"][1]; ?>">Play</button>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
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
	<div class="col-md-6">
        <?php if(isset($previousTrack)) : ?>
			<div class="prevtrack piechart"></div>
            <?php echo sprintf('<i class="fa fa-step-backward"></i> This track is preceded by "%s"', $this->Html->link($previousTrack["title"], array('controller' => 'tracks', 'action' => 'view', $previousTrack["slug"]))); ?>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
		<?php if(isset($nextTrack)) : ?>
			<div class="nexttrack piechart"></div>

			<?php echo sprintf('It is followed by "%s" <i class="fa fa-step-forward"></i>', $this->Html->link($nextTrack["title"], array('controller' => 'tracks', 'action' => 'view', $nextTrack["slug"]))); ?>
        <?php endif; ?>
	</div>
</div>

<div class="review-line even graph">
	<div class="container container-fluid">
		<div class="d3chart big-graph"></div>
	</div>
</div>

*/ ?>

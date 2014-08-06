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

        <h1><?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?></h1>

        <h2><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></h2>

        <?php if((int)$album["release_date"] > 0) : ?>
            <time datetime="<?php echo date("c", $album["release_date"]); ?>"><?php echo date("F j Y", $album["release_date"]); ?></time>
        <?php endif; ?>

        <div class="score">
            <?php if(!is_null($albumReviewSnapshot["score"])) : ?>
                <?php echo (int)($albumReviewSnapshot["score"] * 100); ?>%
            <?php else : ?>
                N/A
            <?php endif; ?>
            <span><?php echo __("Score"); ?></span>
        </div>

        <?php if($this->Chart->isNotableAlbum($album)) : ?>
			<div class="notable"><?php echo __("Notable Album"); ?></div>
        <?php endif; ?>

        <div class="everyone piechart"></div>

        <div class="row stats">

            <div class="col-md-2 enjoyment">
            	<span><?php echo __("Enjoyed"); ?></span>
            	<em>
            		<?php if($this->Chart->isNotAvailable($albumReviewSnapshot)) : ?>
            			N/A
            		<?php else : ?>
            			<?php echo (int)$albumReviewSnapshot["liking_pct"]; ?>%
            		<?php endif; ?>
            	</em>
        	</div>
            <div class="col-md-2 dislike">
            	<span><?php echo __("Disliked"); ?></span>
            	<em>
            		<?php if($this->Chart->isNotAvailable($albumReviewSnapshot)) : ?>
            			N/A
            		<?php else : ?>
            			<?php echo (int)$albumReviewSnapshot["disliking_pct"]; ?>%
            		<?php endif; ?>
            	</em>
        	</div>

        	<?php if(isset($bestTrack)) : ?>
            <div class="col-md-4 best-track">
            	<span><?php echo __("Best track"); ?></span>
        		<em><?php echo $this->Html->link($bestTrack["title"], array('controller' => 'tracks', 'action' => 'view', $bestTrack["slug"])); ?>&nbsp;<?php echo (int)$bestTrack["TrackReviewSnapshot"]["liking_pct"]; ?>%</em>
            </div>
    		<?php endif; ?>

        	<?php if(isset($worstTrack)) : ?>
            <div class="col-md-4 worst-track">
            	<span><?php echo __("Worst track"); ?></span>
        		<em><?php echo $this->Html->link($worstTrack["title"], array('controller' => 'tracks', 'action' => 'view', $worstTrack["slug"])); ?>&nbsp;<?php echo (int)$worstTrack["TrackReviewSnapshot"]["liking_pct"]; ?>%</em>
            </div>
    		<?php endif; ?>

    		<div class="col-md-12 social">

		        <?php if($isLogged) : ?>
		        	<div class="col-md-3">
			        	<h3><?php echo __("Your subscriptions"); ?></h3>
            			<?php if($this->Chart->hasScore($subsAlbumReviewSnapshot)) : ?>
			                <?php echo (int)($subsAlbumReviewSnapshot["score"] * 100); ?>%
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
            			<?php if($this->Chart->hasScore($userAlbumReviewSnapshot)) : ?>
			                <?php echo (int)($userAlbumReviewSnapshot["score"] * 100); ?>%
			            <?php else : ?>
			                N/A
			            <?php endif; ?>
			            <span><?php echo __("Score"); ?></span>
				    </div>
		            <div class="col-md-3">
		            	<div class="piechart you"></div>
				    </div>
		    	<?php else : ?>
					<?php $login = $this->Html->link(__("Login"), array('controller' => 'users', 'action' => 'login', '?' => array("rurl" => '/albums/view/' . $album['slug'])), array("class" => "btn btn-primary")); ?>
					<p><?php echo sprintf(__("%s to see how you and your friends have rated  \"%s\"."), $login, $album["name"]); ?></p>
		    	<?php endif; ?>

    		</div>
        </div>
    </header>

	<?php $wiki = $this->StringMaker->composeAlbumPresentation($lastfmAlbum, $album, $artist); ?>
	<?php if(strlen($wiki) > 0) : ?>
	<div class="row wiki <?php echo strlen($wiki) <= 800 ? "full" : ""; ?>">
		<div class="col-md-12 lead">
			<?php echo substr($wiki, 0, 800); ?></p>
        	<i class="mask"></i>
		</div>
		<div class="col-md-4 lastfm"><a href="http://www.last.fm/"><img src="/img/icon-lastfm.png" alt="Last.fm" title="Last.fm" /></a></div>
        <div class="col-md-4 bug"><span class="report-bug" data-bug-type="album wiki" data-location="album/<?php echo $album["slug"]; ?>" data-user="<?php echo $this->Session->read('Auth.User.User.id'); ?>"><i class="fa fa-bug"></i> <?php echo __("Wrong/weird bio?"); ?></span></div>
        <div class="col-md-4 readmore">
        	<?php if(strlen($wiki) > 800) : ?>
            	<?php echo $this->Html->link("Read more", array('controller' => 'albums', 'action' => 'wiki', $album["slug"]), array("class" => "btn btn-primary")); ?>
        	<?php endif; ?>
        </div>
    </div>
	<?php endif; ?>

	<div class="row content">

        <?php if(count($tracks)) : ?>
        	<h2><?php echo __("Overview"); ?></h2>
        	<div class="row">
		    	<div class="col-md-3">
		            <ul class="tracklisting">
		            <?php foreach ($tracks as $idx => $track) : ?>
		                <li>
	                    	<?php echo $this->Html->link($track["title"], array('controller' => 'tracks', 'action' => 'view', $track["slug"])); ?>
	                    	<div class="piechart track-<?php echo $idx; ?>"></div>
		                </li>
		            <?php endforeach; ?>
		            </ul>
		        </div>
		        <div class="col-md-9 big-graph"></div>
			</div>

	 		<h2><?php echo __("Recent Reviewers"); ?></h2>
	 		<div class="col-md-6">
		        <?php if(count($usersWhoReviewed) > 0) : ?>
		            <ul>
		                <?php foreach($usersWhoReviewed as $user) : ?>
		                <li>
		                    <img src="<?php echo $this->App->getImageUrl($user["User"], true); ?>" alt="<?php $user["User"]["firstname"] . " " . $user["User"]["lastname"]; ?>" />
		                </li>
		                <?php endforeach; ?>
		            </ul>
		        <?php elseif(count($tracks)) : ?>
		        	<?php $login = $this->Html->link(__("Review"), array('controller' => 'tracks', 'action' => 'review', $tracks[0]['slug']), array("class" => "btn btn-primary")); ?>
		            <p><?php echo sprintf(__("Be the first to %s a track off \"%s\"."), $login, $album["name"]); ?></p>
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
	        <?php else : ?>
	    		<?php $login = $this->Html->link(__("Login"), array('controller' => 'users', 'action' => 'login', '?' => array("rurl" => '/albums/view/' . $album['slug'])), array("class" => "btn btn-primary")); ?>
				<p><?php echo sprintf(__("%s to see which of your friends have rated  \"%s\"."), $login, $album["name"]); ?></p>
	        <?php endif; ?>

	        <?php else : ?>
	        	<div class="col-md-12">
	            	<p class="lead"><?php echo __("We could not fetch the album's tracks from the API for the moment."); ?></p>
	            	 <div class="loading-wrap">
			            <i class="fa fa-cog fa-spin fa-fw"></i>
			        </div>
            	</div>
	        <?php endif; ?>
        </div>
	</div>
</article>

<section class="credits">
    <div class="container container-fluid">
        <p>
            <?php echo __("Album description courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>.
            <?php echo __("They were last updated on"); ?> <?php echo date("F j, g:i a", $lastfmAlbum["lastsync"]); ?>.
            <?php echo __("User-contributed text is available under the Creative Commons By-SA License and may also be available under the GNU FDL."); ?>
        </p>
    </div>
</section>

<?php if ((int)$lastfmAlbum["lastsync"] > 0) : ?>
<script>$(function(){
    var svg = d3.select(".big-graph").append("svg");
    <?php if(isset($albumReviewSnapshot)) : ?>
        tmt.createRange(svg, <?php echo json_encode($albumReviewSnapshot["ranges"]); ?>, {key: "everyone range-everyone", total: <?php echo (int)$album["duration"]; ?>});
        tmt.createLine(svg, <?php echo json_encode($albumReviewSnapshot["curve"]); ?>, {key: "everyone line-everyone", total: <?php echo (int)$album["duration"]; ?>});
        tmt.createPie(".everyone.piechart", [{"type" : "smile", "value" : <?php echo (int)$albumReviewSnapshot["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo (int)$albumReviewSnapshot["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo (int)$albumReviewSnapshot["disliking_pct"]; ?>}], {key: "tanker chart-tanker"});
    <?php endif; ?>
    <?php if(isset($userAlbumReviewSnapshot)) : ?>
        tmt.createRange(svg, <?php echo json_encode($userAlbumReviewSnapshot["ranges"]); ?>, {key: "user range-user", total: <?php echo (int)$album["duration"]; ?>});
        tmt.createLine(svg, <?php echo json_encode($userAlbumReviewSnapshot["curve"]); ?>, {key: "user line-user", total: <?php echo (int)$album["duration"]; ?>});
        tmt.createPie(".uars.piechart", [{"type" : "smile", "value" : <?php echo (int)$userAlbumReviewSnapshot["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo (int)$userAlbumReviewSnapshot["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo (int)$userAlbumReviewSnapshot["disliking_pct"]; ?>}], {key: "tanker chart-tanker"});
    <?php endif; ?>
    <?php if(isset($profileAlbumReviewSnapshot)) : ?>
        tmt.createRange(svg, <?php echo json_encode($profileAlbumReviewSnapshot["ranges"]); ?>, {key: "profile range-profile", total: <?php echo (int)$album["duration"]; ?>});
        tmt.createLine(svg, <?php echo json_encode($profileAlbumReviewSnapshot["curve"]); ?>, {key: "profile line-profile", total: <?php echo (int)$album["duration"]; ?>});
    <?php endif; ?>
});</script>
<?php endif; ?>

<nav class="sub-menu">
    <ol class="breadcrumb">
        <li><?php echo $this->Html->link(__("Artists"), array('controller' => 'artists', 'action' => 'index')); ?></li>
        <li><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></li>
        <li class="active"><?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?></li>
    </ol>
</nav>

<section class="jumbotron colored introduction">
	<div class="container container-fluid">
		<div class="row">
			<div class="col-md-3 thumbnail">
	            <?php echo $this->Html->image( $this->App->getImageUrl($album, true), array("alt" => $album["name"])); ?>
	        </div>
	        <div class="col-md-8 col-md-offset-1">
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


<div class="review-line appreciation odd">
	<div class="container container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="trs piechart"></div>
				<p><?php echo $this->StringMaker->composeAlbumAppreciation($albumReviewSnapshot, $album, $artist); ?></p>
				<p><?php echo $this->StringMaker->composeTimedAppreciation($albumReviewSnapshot, /*$album["duration"]*/260*12); ?></p>
			</div>
		</div>
	</div>
</div>

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
                        <p><?php echo __("None of your subscriptions have reviewed a track on this album."); ?></p>
                    <?php endif; ?>
                <?php endif; ?>
	        </div>
		</div>
	</div>
</div>


    <?php  //echo $this->element("stats"); ?>



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
			<p><?php echo __("We have trouble connecting to the api and cannot load the tracks at the moment. Sorry for the inconvenience."); ?></p>
		<?php endif; ?>
	</div>
</div>


<section class="credits">
    <div class="container container-fluid">
        <p>
            <?php echo __("Album description courtesy of"); ?> <?php echo $this->Html->link("Last.fm", "http://www.last.fm/", array("target" => "_blank")); ?>.
            <?php echo __("They were last updated on"); ?> <?php echo date("F j, g:i a", $lastfmAlbum["lastsync"]); ?>.
        </p>
    </div>
</section>

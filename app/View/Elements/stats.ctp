
<?php if(isset($artistReviewSnapshot) && count($artistReviewSnapshot)) : ?>
	<section class="tankers">
	    <?php echo $this->Chart->getBigPie("track", $artist["slug"], $artistReviewSnapshot); ?>
	    <h3><?php echo __("General"); ?></h3>
	    <ul>
	        <li class="average"><?php echo $this->Chart->formatScore($artistReviewSnapshot["score_snapshot"]); ?></li>
	        <li class="enjoyment"><?php echo $this->Chart->formatPct($artistReviewSnapshot["liking_pct"]); ?><i class="fa fa-smile-o"></i></li>
	        <li class="neutral"><?php echo $this->Chart->formatPct($artistReviewSnapshot["neutral_pct"]); ?><i class="fa fa-meh-o"></i></li>
	        <li class="displeasure"><?php echo $this->Chart->formatPct($artistReviewSnapshot["disliking_pct"]); ?><i class="fa fa-frown-o"></i></li>
	    </ul>
	</section>
<?php endif; ?>

<?php if(isset($userArtistReviewSnapshot)) : ?>
    <section class="subscribers">
        <h3><?php echo __("Subscriptions"); ?></h3>
        <?php echo $this->Chart->getBigPie("track", $artist["slug"], $userArtistReviewSnapshot); ?>
        <ul>
            <li class="average"><?php echo $this->Chart->formatScore($userArtistReviewSnapshot["score_snapshot"]); ?></li>
            <li class="enjoyment"><?php echo $this->Chart->formatPct($userArtistReviewSnapshot["liking_pct"]); ?><i class="fa fa-smile-o"></i></li>
            <li class="neutral"><?php echo $this->Chart->formatPct($userArtistReviewSnapshot["neutral_pct"]); ?><i class="fa fa-meh-o"></i></li>
            <li class="displeasure"><?php echo $this->Chart->formatPct($userArtistReviewSnapshot["disliking_pct"]); ?><i class="fa fa-frown-o"></i></li>
        </ul>
    </section>
<?php endif; ?>

<?php if(isset($trackReviewSnapshot)) : ?>
	<section class="tankers">
		<?php echo $this->Chart->getBigPie("track", $track["slug"], $trackReviewSnapshot); ?>
		<h3><?php echo __("General"); ?></h3>
		<ul>
			<li class="average"><?php echo $this->Chart->formatScore($trackReviewSnapshot["score_snapshot"]); ?></li>
			<li class="enjoyment"><?php echo $this->Chart->formatPct($trackReviewSnapshot["liking_pct"]); ?><br>:)</li>
			<li class="displeasure"><?php echo $this->Chart->formatPct($trackReviewSnapshot["disliking_pct"]); ?><br>:(</li>
		</ul>
	</section>
<?php endif; ?>

<?php if(isset($profileTrackReviewSnapshot) && isset($viewingUser)) : ?>
<section class="tankers">
	<?php echo $this->Chart->getBigPie("track", $track["slug"], $profileTrackReviewSnapshot); ?>
	<h3><?php echo $viewingUser["firstname"] . " " . $viewingUser["lastname"]; ?></h3>
	<ul>
		<li class="average"><?php echo $this->Chart->formatScore($profileTrackReviewSnapshot["score_snapshot"]); ?></li>
		<li class="enjoyment"><?php echo $this->Chart->formatPct($profileTrackReviewSnapshot["liking_pct"]); ?><br>:)</li>
		<li class="displeasure"><?php echo $this->Chart->formatPct($profileTrackReviewSnapshot["disliking_pct"]); ?><br>:(</li>
	</ul>
</section>
<?php endif; ?>

<?php if(isset($userTrackReviewSnapshot)) : ?>
	<section class="you">
		<?php echo $this->Chart->getBigPie("track", $track["slug"], $userTrackReviewSnapshot); ?>
		<h3><?php echo __("You"); ?></h3>
		<ul>
			<li class="average"><?php echo $this->Chart->formatScore($userTrackReviewSnapshot["score_snapshot"]); ?></li>
			<li class="enjoyment"><?php echo $this->Chart->formatPct($userTrackReviewSnapshot["liking_pct"]); ?><br>:)</li>
			<li class="displeasure"><?php echo $this->Chart->formatPct($userTrackReviewSnapshot["disliking_pct"]); ?><br>:(</li>
		</ul>
	</section>
<?php endif; ?>

<?php if(isset($subsTrackReviewSnapshot) && count($subsTrackReviewSnapshot)) : ?>
	<section class="subs">
		<?php echo $this->Chart->getBigPie("track", $track["slug"], $subsTrackReviewSnapshot); ?>
		<h3><?php echo __("Subscribers"); ?></h3>
		<ul>
			<li class="average"><?php echo $this->Chart->formatScore($subsTrackReviewSnapshot["score_snapshot"]); ?></li>
			<li class="enjoyment"><?php echo $this->Chart->formatPct($subsTrackReviewSnapshot["liking_pct"]); ?><br>:)</li>
			<li class="displeasure"><?php echo $this->Chart->formatPct($subsTrackReviewSnapshot["disliking_pct"]); ?><br>:(</li>
		</ul>
	</section>
<?php endif; ?>

<?php if(isset($albumReviewSnapshot)) : ?>
<section class="tankers">
    <?php echo $this->Chart->getBigPie("album", $album["slug"] . "-1", $albumReviewSnapshot); ?>
    <h3><?php echo __("General"); ?></h3>
    <?php /* <p><?php echo __("Metacritic score"); ?> <?php echo $this->Chart->formatScore($albumReviewSnapshot["metacritic_score"]); ?></p> */ ?>
    <ul>
        <li class="average"><?php echo $this->Chart->formatScore($albumReviewSnapshot["score_snapshot"]); ?></li>
        <li class="enjoyment"><?php echo $this->Chart->formatPct($albumReviewSnapshot["liking_pct"]); ?><i class="fa fa-smile-o"></i></li>
        <li class="neutral"><?php echo $this->Chart->formatPct($albumReviewSnapshot["neutral_pct"]); ?><i class="fa fa-meh-o"></i></li>
        <li class="displeasure"><?php echo $this->Chart->formatPct($albumReviewSnapshot["disliking_pct"]); ?><i class="fa fa-frown-o"></i></li>
    </ul>
</section>
<?php endif; ?>

<?php if(isset($subsAlbumReviewSnapshot)) : ?>
	<section class="subscribers">
        <?php echo $this->Chart->getBigPie("album", $album["slug"] . "-2", $albumReviewSnapshot); ?>
    <?php if(count($subsAlbumReviewSnapshot) > 0) : ?>
        <h3><?php echo __("Subscriptions"); ?></h3>
        <ul>
             <li class="average"><?php echo $this->Chart->formatScore($albumReviewSnapshot["score_snapshot"]); ?></li>
            <li class="enjoyment"><?php echo $this->Chart->formatPct($subsAlbumReviewSnapshot["liking_pct"]); ?><i class="fa fa-smile-o"></i></li>
            <li class="neutral"><?php echo $this->Chart->formatPct($subsAlbumReviewSnapshot["neutral_pct"]); ?><i class="fa fa-meh-o"></i></li>
            <li class="displeasure"><?php echo $this->Chart->formatPct($subsAlbumReviewSnapshot["disliking_pct"]); ?><i class="fa fa-frown-o"></i></li>
        </ul>
    <?php else : ?>
        <p><?php echo __("None of the people you are subscribed to have reviewed this album yet."); ?></p>
    <?php endif; ?>
</section>
<?php endif; ?>

<?php if(isset($userAlbumReviewSnapshot)) : ?>
<section class="you">
    <?php echo $this->Chart->getBigPie("album", $album["slug"] . "-3", $userAlbumReviewSnapshot); ?>
    <?php if(count($userAlbumReviewSnapshot) > 0) : ?>
        <h3><?php echo __("Your rating"); ?></h3>
        <ul>
            <li class="average"><?php echo $this->Chart->formatScore($userAlbumReviewSnapshot["score_snapshot"]); ?></li>
            <li class="enjoyment"><?php echo $this->Chart->formatPct($userAlbumReviewSnapshot["liking_pct"]); ?><i class="fa fa-smile-o"></i></li>
            <li class="neutral"><?php echo $this->Chart->formatPct($userAlbumReviewSnapshot["neutral_pct"]); ?><i class="fa fa-meh-o"></i></li>
            <li class="displeasure"><?php echo $this->Chart->formatPct($userAlbumReviewSnapshot["disliking_pct"]); ?><i class="fa fa-frown-o"></i></li>
        </ul>
    <?php else : ?>
        <p><?php echo __("You have not reviewed this album yet."); ?></p>
    <?php endif; ?>
</section>
<?php endif; ?>

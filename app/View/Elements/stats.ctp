<section class="tankers">
	<?php echo $this->Chart->getBigPie("track", $track["slug"], $trackReviewSnapshot); ?>
	<h3><?php echo __("General"); ?></h3>
	<ul>
		<li class="average"><?php echo $this->Chart->formatScore($trackReviewSnapshot["score_snapshot"]); ?></li>
		<li class="enjoyment"><?php echo $this->Chart->formatPct($trackReviewSnapshot["liking_pct"]); ?><br>:)</li>
		<li class="displeasure"><?php echo $this->Chart->formatPct($trackReviewSnapshot["disliking_pct"]); ?><br>:(</li>
	</ul>
</section>

<?php if(isset($viewingUser)) : ?>
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


<?php if(isset($subsTrackReviewSnapshot)) : ?>
	<section class="you">
		<?php echo $this->Chart->getBigPie("track", $track["slug"], $subsTrackReviewSnapshot); ?>
		<h3><?php echo __("You"); ?></h3>
		<ul>
			<li class="average"><?php echo $this->Chart->formatScore($subsTrackReviewSnapshot["score_snapshot"]); ?></li>
			<li class="enjoyment"><?php echo $this->Chart->formatPct($subsTrackReviewSnapshot["liking_pct"]); ?><br>:)</li>
			<li class="displeasure"><?php echo $this->Chart->formatPct($subsTrackReviewSnapshot["disliking_pct"]); ?><br>:(</li>
		</ul>
	</section>
<?php endif; ?>

<?php
    $currentUserId = $this->Session->read('Auth.User.User.id');  
?>
<div class="container container-fluid">

	<?php echo $this->element('profilesmenu'); ?>

	<h2><?php echo __("Unlocked achievements"); ?></h2>
	<p class="lead"><?php echo sprintf(__("The list of all achievements unlocked by %s."), $user["firstname"] . " " . $user["lastname"]); ?></p>

	<div class="row achievements">
		<?php if(count($achievements) > 0) : ?>
		    <?php foreach($achievements as $achievement) : ?>
		        <div class="col-xs-12 col-md-3">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><?php echo $achievement["Achievement"]["name"]; ?></h3>
						</div>
						<div class="panel-body">
	                        <blockquote><?php echo $achievement["Achievement"]["description"]; ?></blockquote>
						</div>
						<div class="panel-footer">
	        				<?php echo __("Unlocked on "); ?> <?php echo date("m/d/Y", $achievement["UserAchievements"]["created"]); ?>
						</div>
					</div>
				</div>
		    <?php endforeach; ?>
		<?php else : ?>
			<div class="col-md-12">
				<p><?php echo __("User has no followers at the moment."); ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>
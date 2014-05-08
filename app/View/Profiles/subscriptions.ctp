<?php
    $currentUserId = $this->Session->read('Auth.User.User.id');  
?>
<div class="container container-fluid">

	<?php echo $this->element('profilesmenu'); ?>

	<h2><?php echo __("Subscriptions"); ?></h2>
	<p class="lead"><?php echo sprintf(__("The list of all people %s is currently following."), $user["firstname"] . " " . $user["lastname"]); ?></p>

	<div class="row">
		<?php if(count($subscriptions) > 0) : ?>
		    <?php foreach($subscriptions as $follower) :  ?>    		    	
	        	<div class="col-xs-12 col-md-3">

					<div class="panel panel-default user-badge">
						<div class="panel-heading">
							<h3 class="panel-title"><?php echo $this->Html->link($follower["User"]["firstname"] . " " . $follower["User"]["lastname"], array('controller' => 'profiles', 'action' => 'view', $follower["User"]["slug"])); ?></h3>						
						</div>
						<div class="panel-body"> 
						    <?php echo $this->Html->image($this->App->getImageUrl($user), array("class" => "img-circle", "alt" => $user["firstname"] . " " . $user["lastname"], "title" => $user["firstname"] . " " . $user["lastname"])); ?>   
						</div>						
						<?php if($follower["User"]["id"] !== $currentUserId) : ?>
						<ul class="list-group">
		                    <li class="list-group-item">
								<?php echo $this->element('followButton', array("user" => $follower["User"])); ?>
		                    </li>
		                </ul>
		            	<?php endif; ?>
	                </div>

		    	</div>
		    <?php endforeach; ?>
		<?php else : ?>
			<div class="col-md-12">
				<p><?php echo __("User has no subscriptions at the moment."); ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>
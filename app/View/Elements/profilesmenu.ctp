<section class="jumbotron colored big-user-badge">
	<div class="container">
		<div class="row">
		    <div class="col-md-3">
		        <?php echo $this->Html->image($this->App->getImageUrl($user), array("class" => "img-circle", "alt" => $user["firstname"] . " " . $user["lastname"], "title" => $user["firstname"] . " " . $user["lastname"])); ?>
		    </div>
		    <div class="col-md-9">
		        <h2><?php echo $user["firstname"]; ?> <?php echo $user["lastname"]; ?></h2>
		        <p class="lead"><?php echo __('Joined The Music Tank on'); ?> <?php echo $this->Time->timeAgoInWords($user['created']); ?>.</p>
		        <?php /*<ul>
		            <li><?php echo $this->Html->link(__("@francoisfaubert"), "http://www.twitter.com/francoisfaubert"); ?></li>
		            <li><?php echo $this->Html->link(__("Profile"), array("controller" => "users", "action" => "view", "francois")); ?></li>
		        </ul> */ ?>
		        <?php if($this->Session->read('Auth.User.User.id') != $user["id"]) : ?>
		            <p><?php echo $this->element('followButton', array("user" => $user)); ?></p>
		        <?php endif;?>
		    </div>
		</div>
	</div>
</section>

<div class="row">
	<nav class="dashboard">
	    <ul class="nav nav-tabs">
	        <li <?php if($this->params["action"] == "view") : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__("Activity"), array('controller' => 'profiles', 'action' => 'view', $user["slug"])); ?> </li>
	        <li <?php if($this->params["action"] == "achievements") : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__("Achievements"), array('controller' => 'profiles', 'action' => 'achievements', $user["slug"])); ?> </li>
	        <li <?php if($this->params["action"] == "subscriptions") : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__("Subscriptions"), array('controller' => 'profiles', 'action' => 'subscriptions', $user["slug"])); ?></li>
	        <li <?php if($this->params["action"] == "followers") : ?>class="active"<?php endif; ?>><?php echo $this->Html->link(__("Followers"), array('controller' => 'profiles', 'action' => 'followers', $user["slug"])); ?> </li>
	    </ul>
	</nav>
</div>

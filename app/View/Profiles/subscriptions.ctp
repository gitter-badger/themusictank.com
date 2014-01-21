<?php echo $this->element('profilesmenu'); ?>

<h2><?php echo __("Subscriptions"); ?></h2>

<?php if(count($subscriptions) > 0) : ?>
<ul class="following">    
    <?php foreach($subscriptions as $follower) :  ?>    
    <li class="following"> 
       	<?php echo $this->Html->link($follower["User"]["firstname"] . " " . $follower["User"]["lastname"], array('controller' => 'profiles', 'action' => 'view', $follower["User"]["slug"])); ?>
    	<?php echo $this->element('followButton', array("user" => $follower["User"])); ?>
    </li>
    <?php endforeach; ?>    
</ul>
<?php else : ?>
	<p><?php echo __("User has no followers at the moment."); ?></p>
<?php endif; ?>
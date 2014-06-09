<?php
    $currentUserId = $this->Session->read('Auth.User.User.id');
?>
<div class="panel panel-default user-badge">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $this->Html->link($user["User"]["firstname"] . " " . $user["User"]["lastname"], array('controller' => 'profiles', 'action' => 'view', $user["User"]["slug"])); ?></h3>
	</div>
	<div class="panel-body">
		<?php  echo $this->Html->link(
                $this->Html->image($this->App->getImageUrl($user["User"]), array("class" => "img-circle", "alt" => $user["User"]["firstname"] . " " . $user["User"]["lastname"], "title" => $user["User"]["firstname"] . " " . $user["User"]["lastname"])),
                array('controller' => 'profiles', 'action' => 'view', $user["User"]["slug"]),
                array('escape' => false)
        ); ?>

	</div>
	<?php if($user["User"]["id"] !== $currentUserId) : ?>
	<ul class="list-group">
        <li class="list-group-item">
			<?php echo $this->element('followButton', array("user" => $user["User"])); ?>
        </li>
    </ul>
	<?php endif; ?>
</div>
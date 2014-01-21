<?php echo $this->element('profilesmenu'); ?>

<h2><?php echo __("Followers"); ?></h2>

<ul class="followers">    
    <?php foreach($followers as $follow) :  ?>    
    <li class="follower"> 
        <?php echo $this->Html->link($follow["User"]["firstname"] . " " . $follow["User"]["lastname"], array('controller' => 'profiles', 'action' => 'view', $follow["User"]["username"])); ?>
    </li>
    <?php endforeach; ?>    
</ul>
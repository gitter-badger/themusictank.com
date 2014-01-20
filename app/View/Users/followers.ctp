<?php echo $this->element('dashboardmenu'); ?>

<h2><?php echo __("Your followers"); ?></h2>

<ul class="followers">    
    <?php foreach($followers as $follow) :  ?>    
    <li class="follower"> 
        <?php echo $this->Html->link($follow["User"]["firstname"] . " " . $follow["User"]["lastname"], array('controller' => 'users', 'action' => 'view', $follow["User"]["username"])); ?>
    </li>
    <?php endforeach; ?>    
</ul>
<?php echo $this->element('dashboardmenu'); ?>

<h2><?php echo __("Your user subscriptions"); ?></h2>

<ul class="following">    
    <?php foreach($following as $follow) :  ?>    
    <li class="following"> 
        <?php echo $this->Html->link($follow["User"]["firstname"] . " " . $follow["User"]["lastname"], array('controller' => 'users', 'action' => 'view', $follow["User"]["username"])); ?>
    </li>
    <?php endforeach; ?>    
</ul>
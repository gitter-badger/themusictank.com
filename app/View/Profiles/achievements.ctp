<?php echo $this->element('profilesmenu'); ?>

<h2><?php echo __("Unlocked achievements"); ?></h2>

<ul class="achievements">
    
    <?php foreach($achievements as $achievement) : ?>
    
    <li class="achievement"> 
        <h3><?php echo $achievement["Achievement"]["name"]; ?></h3>
        <p><?php echo $achievement["Achievement"]["description"]; ?></p>
        <p><?php echo __("You have unlocked this on "); ?> <?php echo $achievement["UserAchievements"]["created"]; ?></p>
    </li>
    <?php endforeach; ?>
    
</ul>
<?php $newOnes = 0; ?>

<ul class="notices">
    <?php if(isset($notifications)) :  ?>
        <?php foreach($notifications as $notification) : ?>    
            <?php if(!$notification["Notifications"]["is_viewed"]) $newOnes++; ?>    
            <li class="notification <?php echo $notification["Notifications"]["type"]; ?> <?php echo $notification["Notifications"]["is_viewed"] ? "read" : "new"; ?>"><?php echo $notification["Notifications"]["title"]; ?></li>
        <?php endforeach; ?>
    <?php else : ?>
        <li class="loading"><?php echo __("Loading"); ?>...</li>
    <?php endif; ?>
        
    <?php if($newOnes > 0) : ?>
        <li class="mark"><?php echo $this->Html->link(__("Mark as read"), array('controller' => 'users', 'action' => 'okstfu')); ?></li>
    <?php endif; ?>
        
    <li class="view"><?php echo $this->Html->link(__("View all notifications"), array('controller' => 'users', 'action' => 'notifications')); ?></li>
</ul>

<?php echo $this->Html->link( $newOnes, array('controller' => 'users', 'action' => 'notifications')); ?>

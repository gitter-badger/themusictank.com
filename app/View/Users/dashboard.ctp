<h2><?php echo __("Home"); ?></h2>

<section class="activity">
    
    <?php if(count($feed) > 0) : ?>
    <ul>
        <?php foreach($feed as $event) : ?>
        
            <?php if(array_key_exists("Achievement", $event["UserActivity"])) : ?>
                <li class="achievement">
                    <p>
                        <?php echo $this->Html->link($event["UserActivity"]["User"]["firstname"] . " " . $event["UserActivity"]["User"]["lastname"], array('controller' => 'profiles', 'action' => 'view', $event["UserActivity"]["User"]["slug"])); ?>
                        <?php echo __("has unlocked"); ?>
                        <strong><?php echo $event["UserActivity"]["Achievement"]["name"]; ?></strong>
                    </p>
                    .
                    <span><?php echo date("F j, Y, g:i a", strtotime($event["UserActivity"]["created"])); ?></span>
                </li>

            <?php elseif(array_key_exists("UserFollower", $event["UserActivity"])) : ?>            
                <li class="subscription">
                        <p>
                            <?php echo $this->Html->link($event["UserActivity"]["UserFollower"]["firstname"] . " " . $event["UserActivity"]["UserFollower"]["lastname"], array('controller' => 'profiles', 'action' => 'view', $event["UserActivity"]["UserFollower"]["slug"])); ?>
                            <?php echo __("has subscribed to"); ?>
                            
                            <?php if($event["UserActivity"]["UserFollower"]["id"] === $this->Session->read('Auth.User.User.id')) : ?>
                                <?php echo __("you"); ?>
                            <?php else : ?>
                                <?php echo $this->Html->link($event["UserActivity"]["UserFollower"]["firstname"] . " " . $event["UserActivity"]["UserFollower"]["lastname"], array('controller' => 'profiles', 'action' => 'view', $event["UserActivity"]["UserFollower"]["slug"])); ?>
                            <?php endif; ?>
                            .
                        </p>
                        <span><?php echo date("F j, Y, g:i a", strtotime($event["UserActivity"]["created"])); ?></span>
                    </li>
            <?php endif; ?>
        
        <?php endforeach; ?>
    </ul>
    <?php else : ?>
    
        <p><?php echo __("Nothing is happening."); ?></p>
    
    <?php endif; ?>
    
</section>
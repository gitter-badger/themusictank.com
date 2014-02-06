<?php
    $currentUserId = $this->Session->read('Auth.User.User.id');
    $todaysDayNumber = date("z");
    $currentDay = -1;
    $lastHeader = -1;
?>

<h2><?php echo __("Activity stream"); ?></h2>

<section class="activity">    
    <?php if(count($feed) > 0) : ?>    
        <?php foreach($feed as $event) : ?>
            <?php $currentDay = date("z", $event["UserActivity"]["created"]); ?>
            <?php if($currentDay != $lastHeader) : ?>           
                <header class="time-header">
                    <time datetime="<?php echo $this->Time->i18nFormat($event["UserActivity"]["created"]); ?>" data-title="true" title="<?php echo $this->Time->niceShort($event["UserActivity"]["created"]); ?>">
                        <?php echo $this->Time->timeAgoInWords($event["UserActivity"]["created"], array('accuracy' => array('day' => 'day'), 'end' => '1 month')); ?>
                    </time>
                </header>
                <?php $lastHeader = $currentDay; ?>
            <?php endif; ?>

            <?php if(array_key_exists("Achievement", $event["UserActivity"])) : ?>
                <div class="achievement">                
                    <?php if($event["User"]["id"] === $currentUserId) : ?>
                        <p><?php echo sprintf(__("You have unlocked the achievement \"%s\".", $event["UserActivity"]["Achievement"]["name"])); ?></p>
                    <?php else : ?>
                        <p>
                            <?php echo $this->Html->link(sprintf("%s %s", $event["UserActivity"]["User"]["firstname"], $event["UserActivity"]["User"]["lastname"]), array('controller' => 'profiles', 'action' => 'view', $event["UserActivity"]["UserFollower"]["slug"])); ?> 
                            <?php echo sprintf(__(" has unlocked the achievement \"%s\"."), $event["UserActivity"]["Achievement"]["name"]); ?>
                        </p>
                        <blockquote>
                            "<?php echo $event["UserActivity"]["Achievement"]["description"]; ?>"
                        </blockquote>
                    <?php endif; ?>
                </div>

            <?php elseif(array_key_exists("UserFollower", $event["UserActivity"])) : ?>            
                <div class="subscription">             
                        <p>
                            <?php if($event["User"]["id"] === $currentUserId) : ?>
                                <?php echo __("You have subscribed to "); ?>
                                <?php echo $this->Html->link($event["UserActivity"]["UserFollower"]["firstname"] . " " . $event["UserActivity"]["UserFollower"]["lastname"], array('controller' => 'profiles', 'action' => 'view', $event["UserActivity"]["UserFollower"]["slug"])); ?>                            
                            <?php else : ?>
                                <?php echo $this->Html->link($event["UserActivity"]["UserFollower"]["firstname"] . " " . $event["UserActivity"]["UserFollower"]["lastname"], array('controller' => 'profiles', 'action' => 'view', $event["UserActivity"]["UserFollower"]["slug"])); ?>
                                <?php echo __(" has subscribed to "); ?>                            
                                <?php if($event["UserActivity"]["UserFollower"]["id"] === $currentUserId) : ?>
                                    <?php echo __("you."); ?>
                                <?php else : ?>
                                    <?php echo $this->Html->link($event["UserActivity"]["UserFollower"]["firstname"] . " " . $event["UserActivity"]["UserFollower"]["lastname"], array('controller' => 'profiles', 'action' => 'view', $event["UserActivity"]["UserFollower"]["slug"])); ?>.
                                <?php endif; ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    
            <?php endif; ?>        
        <?php endforeach; ?>
    <?php else : ?>    
        <p><?php echo __("Nothing is happening."); ?></p>    
    <?php endif; ?>    
</section>
<?php
    $currentUserId = $this->Session->read('Auth.User.User.id');
    $currentUserSlug = $this->Session->read('Auth.User.User.slug');
    $todaysDayNumber = date("z");
    $currentDay = -1;
    $lastHeader = -1;
?>

<h2><?php echo __("Activity stream"); ?></h2>

<section class="activity-stream">    

    <?php if(count($feed) > 0) : ?>    
        <?php foreach($feed as $idx => $event) : ?>
            <?php 
                $currentDay = date("z", (int)$event["UserActivity"]["created"]); 
            ?>
            <?php if($currentDay != $lastHeader) : ?>           
                <header class="time-header">
                    <time datetime="<?php echo $this->Time->i18nFormat($event["UserActivity"]["created"]); ?>" data-title="true" title="<?php echo $this->Time->niceShort($event["UserActivity"]["created"]); ?>">
                        <?php echo $this->Time->timeAgoInWords($event["UserActivity"]["created"], array('accuracy' => array('day' => 'day'), 'end' => '1 month')); ?>
                    </time>
                </header>
                <?php $lastHeader = $currentDay; ?>
            <?php endif; ?>


            <div class="event <?php echo ($idx % 2 === 0) ? 'right' : 'left'; ?>">

            <?php if(array_key_exists("Achievement", $event["UserActivity"])) : ?>

                <div class="popoverbox achievement">
                    <div class="popover <?php echo ($idx % 2 === 0) ? 'right' : 'left'; ?>">
                        <div class="arrow"></div>
                        <h3 class="popover-title">
                            <?php if($event["User"]["id"] === $currentUserId) : ?>
                                <?php echo sprintf(__("You have unlocked the achievement \"%s\".", $event["UserActivity"]["Achievement"]["name"])); ?>
                            <?php else : ?>                                
                                <?php echo $this->Html->link(sprintf("%s %s", $event["UserActivity"]["User"]["firstname"], $event["UserActivity"]["User"]["lastname"]), array('controller' => 'profiles', 'action' => 'view', $event["UserActivity"]["UserFollower"]["slug"])); ?> 
                                <?php echo sprintf(__(" has unlocked the achievement \"%s\"."), $event["UserActivity"]["Achievement"]["name"]); ?>                                
                            <?php endif; ?>
                        </h3>
                        <div class="popover-content" data->
                            <blockquote><?php echo $event["UserActivity"]["Achievement"]["description"]; ?></blockquote>
                        </div>
                    </div>
                </div>


            <?php elseif(array_key_exists("UserFollower", $event["UserActivity"])) : ?>            

                <div class="popoverbox subscription">
                    <div class="popover <?php echo ($idx % 2 === 0) ? 'right' : 'left'; ?>">
                        <div class="arrow"></div>
                        <h3 class="popover-title">
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
                        </h3>
                        <div class="popover-content">
                            <p>
                                Print user profile header
                                <?php /*
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
                                <?php endif; ?> */
                                ?>

                            </p>
                        </div>
                    </div>
                </div>

    
            <?php elseif(array_key_exists("ReviewedTrack", $event["UserActivity"])) : ?>

                <div class="popoverbox review">
                    <div class="popover <?php echo ($idx % 2 === 0) ? 'right' : 'left'; ?>">
                        <div class="arrow"></div>
                        <h3 class="popover-title">
                            <?php if($event["User"]["id"] === $currentUserId) : ?>
                                <?php echo __("You have reviewed "); ?>
                                <?php echo $this->Html->link($event["UserActivity"]["ReviewedTrack"]["title"], array('controller' => 'tracks', 'action' => 'by_user', $event["UserActivity"]["ReviewedTrack"]["slug"], $currentUserSlug)); ?>                            
                            <?php else : ?>
                                <?php echo $this->Html->link($event["UserActivity"]["UserFollower"]["firstname"] . " " . $event["UserActivity"]["UserFollower"]["lastname"], array('controller' => 'profiles', 'action' => 'view', $event["UserActivity"]["UserFollower"]["slug"])); ?>
                                <?php echo __(" has reviewed to "); ?>                            
                                <?php echo $this->Html->link($event["UserActivity"]["ReviewedTrack"]["title"], array('controller' => 'tracks', 'action' => 'by_user', $event["UserActivity"]["ReviewedTrack"]["slug"], $event["UserActivity"]["UserFollower"]["slug"])); ?>                                                       
                            <?php endif; ?>
                        </h3>
                        <div class="popover-content">
                            Print track details here.
                        </div>
                    </div>
                </div>

   
            <?php endif; ?>   
            </div>   
        <?php endforeach; ?>
    <?php else : ?>    
        <p><?php echo __("Nothing is happening."); ?></p>    
    <?php endif; ?>    

                <div class="clearfix"></div>
</section>
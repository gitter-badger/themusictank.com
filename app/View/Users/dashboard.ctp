<?php
    $currentUserId = $this->Session->read('Auth.User.User.id');
    $currentUserSlug = $this->Session->read('Auth.User.User.slug');
    $todaysDayNumber = date("z");
    $currentDay = -1;
    $lastHeader = -1;
?>

<div class="container container-fluid">

    <div class="row">

        <div class="col-md-8">

            <section class="activity-stream">    

                <header>
                    <h2><?php echo __("Activity stream"); ?></h2>
                </header>

                <?php if(count($feed) > 0) : ?>    
                    <?php foreach($feed as $idx => $event) : ?>
                        <?php 
                            $currentDay = date("z", (int)Hash::get($event, "UserActivity.created")); 
                        ?>
                        <?php if($currentDay != $lastHeader) : ?>           
                            <header class="time-header">
                                <time datetime="<?php echo $this->Time->i18nFormat(Hash::get($event, "UserActivity.created")); ?>" data-title="true" title="<?php echo $this->Time->niceShort(Hash::get($event, "UserActivity.created")); ?>">
                                    <?php echo $this->Time->timeAgoInWords(Hash::get($event, "UserActivity.created"), array('accuracy' => array('day' => 'day'), 'end' => '1 month')); ?>
                                </time>
                            </header>
                            <?php $lastHeader = $currentDay; ?>
                        <?php endif; ?>

                        <div class="event <?php echo ($idx % 2 === 0) ? 'right' : 'left'; ?>">
                        <?php if(Hash::check($event, "UserActivity.Achievement")) : ?>

                            <div class="popoverbox achievement">
                                <div class="popover <?php echo ($idx % 2 === 0) ? 'right' : 'left'; ?>">
                                    <div class="arrow"></div>
                                    <h3 class="popover-title">
                                        <?php if($event["User"]["id"] === $currentUserId) : ?>
                                            <?php echo sprintf(__("You have unlocked the achievement \"%s\".", Hash::get($event, "UserActivity.Achievement.name"))); ?>
                                        <?php else : ?>                                
                                            <?php echo $this->Html->link(sprintf("%s %s", Hash::get($event, "UserActivity.User.firstname"), Hash::get($event, "UserActivity.User.lastname")), array('controller' => 'profiles', 'action' => 'view', Hash::get($event, "UserActivity.UserFollower.slug"))); ?> 
                                            <?php echo sprintf(__(" has unlocked the achievement \"%s\"."), Hash::get($event, "UserActivity.Achievement.name")); ?>                                
                                        <?php endif; ?>
                                    </h3>
                                    <div class="popover-content">
                                        <blockquote><?php echo Hash::get($event, "UserActivity.Achievement.description"); ?></blockquote>
                                    </div>
                                </div>
                            </div>

                        <?php elseif(Hash::check($event, "UserActivity.UserFollower")) : 
                            $fullname = Hash::get($event, "UserActivity.UserFollower.firstname") . " " . Hash::get($event, "UserActivity.UserFollower.lastname"); ?>

                            <div class="popoverbox subscription">
                                <div class="popover <?php echo ($idx % 2 === 0) ? 'right' : 'left'; ?>">
                                    <div class="arrow"></div>
                                    <h3 class="popover-title">
                                        <?php if($event["User"]["id"] === $currentUserId) : ?>
                                            <?php echo __("You have subscribed to "); ?>
                                            <?php echo $this->Html->link($fullname, array('controller' => 'profiles', 'action' => 'view', $event["UserActivity"]["UserFollower"]["slug"])); ?>                            
                                        <?php else : ?>
                                            <?php echo $this->Html->link($fullname, array('controller' => 'profiles', 'action' => 'view', $event["UserActivity"]["UserFollower"]["slug"])); ?>
                                            <?php echo __(" has subscribed to "); ?>                            
                                            <?php if($event["UserActivity"]["UserFollower"]["id"] === $currentUserId) : ?>
                                                <?php echo __("you."); ?>
                                            <?php else : ?>
                                                <?php echo $this->Html->link($fullname, array('controller' => 'profiles', 'action' => 'view', $event["UserActivity"]["UserFollower"]["slug"])); ?>.
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </h3>
                                    <div class="popover-content">

                                        <div class="panel panel-default user-badge">
                                            <div class="panel-heading">
                                                <h3 class="panel-title"><?php echo $this->Html->link($fullname, array('controller' => 'profiles', 'action' => 'view', Hash::get($event, "UserActivity.UserFollower.slug"))); ?></h3>
                                            </div>
                                            <div class="panel-body"> 
                                                <?php  echo $this->Html->link(
                                                        $this->Html->image($this->App->getImageUrl(Hash::get($event, "UserActivity.UserFollower")), array("class" => "img-circle", "alt" => $fullname, "title" => $fullname)),
                                                        array('controller' => 'profiles', 'action' => 'view', Hash::get($event, "UserActivity.UserFollower.slug")),
                                                        array('escape' => false)
                                                ); ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                
                        <?php elseif(Hash::check($event, "UserActivity.ReviewedTrack")) : ?>

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
                                            <?php echo $this->Html->link(
                                                    $this->Html->image($this->App->getImageUrl(Hash::get($event, "UserActivity.ReviewedTrackAlbum"), true), array("alt" => Hash::get($event, "UserActivity.ReviewedTrackAlbum.name"), "class" => "thumbnail")),
                                                    array('controller' => 'tracks', 'action' => 'view', Hash::get($event, "UserActivity.ReviewedTrackAlbum.slug")),
                                                    array('escape' => false)
                                            ); ?>
                                        <p>
                                            <?php echo $this->Html->link(Hash::get($event, "UserActivity.ReviewedTrack.title"), array('controller' => 'tracks', 'action' => 'view', Hash::get($event, "UserActivity.ReviewedTrack.slug"))); ?>                
                                            <?php echo __("can be found on"); ?> <?php echo $this->Html->link(Hash::get($event, "UserActivity.ReviewedTrackAlbum.name"), array('controller' => 'albums', 'action' => 'view', Hash::get($event, "UserActivity.ReviewedTrackAlbum.slug"))); ?>
                                            <?php echo __("by"); ?> <?php echo $this->Html->link(Hash::get($event, "UserActivity.ReviewedTrackArtist.name"), array('controller' => 'artists', 'action' => 'view', Hash::get($event, "UserActivity.ReviewedTrackArtist.slug"))); ?>
                                        </p>
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
        </div>

        <aside class="col-md-4 aside">

            <h3><?php echo __("Daily challenge"); ?></h3>
            <p class="lead"><?php echo $this->StringMaker->composeDailyChallengeIntro($dailyChallenge); ?></p>
            <div class="media">
                <?php  echo $this->Html->link(
                        $this->Html->image($this->App->getImageUrl($dailyChallenge["Album"], true), array("alt" => $dailyChallenge["Track"]["title"], "title" => $dailyChallenge["Track"]["title"], "class" => "thumbnail")),
                        array('controller' => 'player', 'action' => 'play', $dailyChallenge["Track"]["slug"]),
                        array('escape' => false, 'class' => "pull-left")
                ); ?>
                <div class="media-body">
                    <h4 class="media-heading"><?php echo $this->Html->link($dailyChallenge["Track"]["title"], array('controller' => 'player', 'action' => 'play', $dailyChallenge["Track"]["slug"])); ?></h4>
                    <p>
                        <?php echo __("Found on"); ?> <?php echo $this->Html->link($dailyChallenge["Album"]["name"], array('controller' => 'albums', 'action' => 'view', $dailyChallenge["Album"]["slug"])); ?>
                        <?php echo __("by"); ?> <?php echo $this->Html->link($dailyChallenge["Album"]["Artist"]["name"], array('controller' => 'artists', 'action' => 'view', $dailyChallenge["Album"]["Artist"]["slug"])); ?>
                    </p>
                    <p><?php echo $this->Html->link(__("Let's do this"), array('controller' => 'player', 'action' => 'play', $dailyChallenge["Track"]["slug"]), array("class" => "btn btn-primary")); ?></p>        
                </div>
            </div>

                <?php /*

            <h3><?php echo __("Recommandations"); ?></h3>
            --- albums<br>
            --- users<br>

                */ ?>
        </div>

    </aside>
    
</div>
<?php echo $this->element('dashboardmenu'); ?>

<h2><?php echo __("Dashboard"); ?></h2>

<div class="cols">
    
    <div class="col col-1-2">

        <h2><?php echo __("On TMT"); ?></h2>
        
        <?php if($dailyChallenge) : ?>
        <div class="daily-challenge">
            <h3><?php echo __("Today's daily track review challenge"); ?></h3>
            <p>
                <?php if(!is_null($dailyChallenge["Album"]["image"])) : ?>
                    <?php echo $this->Html->image($dailyChallenge["Album"]["image"], array("alt" => $dailyChallenge["Album"]["name"], "class" => "thumbnail-small", "height" => 50)); ?>
                <?php endif; ?>
                <?php echo $this->Html->link($dailyChallenge["Album"]["Artist"]["name"], array('controller' => 'artists', 'action' => 'view', $dailyChallenge["Album"]["Artist"]["slug"])); ?> :
                <?php echo $this->Html->link($dailyChallenge["Album"]["name"], array('controller' => 'albums', 'action' => 'view', $dailyChallenge["Album"]["slug"])); ?> :
                <?php echo $this->Html->link($dailyChallenge["Track"]["title"], array('controller' => 'tracks', 'action' => 'view',  $dailyChallenge["Track"]["slug"])); ?>
            </p>
        </div>        
        <?php endif; ?>
        
        
        <?php if(count($newReleases) > 0) : ?>
            <h3><?php echo __("New album releases") ?></h3>
            <p><?php echo __("For the week of") . " " . date("F j Y", $forTheWeekOf); ?></p>
            <ul class="tiled-list new-releases albums">
            <?php foreach($newReleases as $album) : ?>
                <li>
                    <?php if(!is_null($album["Album"]["image"])) : ?>
                        <?php echo $this->Html->link(
                                $this->Html->image($album["Album"]["image"], array("alt" => $album["Album"]["name"], "class" => "thumbnail", "width" => 200, "height" => 200)),
                                array('controller' => 'albums', 'action' => 'view', $album["Album"]["slug"]),
                                array('escape' => false)
                        ); ?>
                    <?php endif; ?>

                    <p><?php echo $this->Html->link($album["Album"]["name"], array('controller' => 'albums', 'action' => 'view', $album["Album"]["slug"])); ?></p>
                    <p><?php echo __("By"); ?> <?php echo $this->Html->link($album["Artist"]["name"], array('controller' => 'artists', 'action' => 'view', $album["Artist"]["slug"])); ?></p>
                    <p><?php echo __("Released"); ?> <?php echo date("F j Y", $album["Album"]["release_date"]); ?></p>
                </li>
            <?php endforeach; ?>
            </ul>            
            <p><?php echo $this->Html->link(__("More"), array('controller' => 'albums', 'action' => 'newreleases')); ?></p>
        <?php endif; ?>        

    </div>    
    
    <div class="col col-2-2">        
        
        <h2><?php echo __("Your Activity"); ?></h2>
        
        <div class="latest-reviews">
            <h3><?php echo __("Recently reviewed"); ?></h3>
            <?php if(count($recentReviews) > 0) : ?>
                <ul>
                <?php foreach($recentReviews as $review) : ?>
                    <li>
                        <?php if(!is_null($review["Album"]["image"])) : ?>
                            <?php echo $this->Html->image($review["Album"]["image"], array("alt" => $review["Album"]["name"], "class" => "thumbnail-small", "height" => 50)); ?>
                        <?php endif; ?>
                        <?php echo $this->Html->link($review["Artist"]["name"], array('controller' => 'artists', 'action' => 'view', $review["Artist"]["slug"])); ?> :
                        <?php echo $this->Html->link($review["Album"]["name"], array('controller' => 'albums', 'action' => 'view', $review["Album"]["slug"])); ?> :
                        <?php echo $this->Html->link($review["Track"]["title"], array('controller' => 'tracks', 'action' => 'view', $review["Track"]["slug"])); ?>
                        <?php echo $this->Chart->getSmallPie("usertrack", $review["Track"]["slug"], $review["appreciation"]); ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p><?php echo __("You have not reviewed anything yet."); ?></p>
            <?php endif; ?>
        </div>
        

        <div class="top-areas">
            <h3><?php echo __("Top rated song spans"); ?></h3>
            <?php if(count($topAreas) > 0) : ?>
                <ul>
                <?php foreach($topAreas as $topArea) : if(array_key_exists("Artist", $topArea["Album"])) : ?>
                <li>
                    <?php if(!is_null($topArea["Album"]["image"])) : ?>
                        <?php echo $this->Html->image($topArea["Album"]["image"], array("alt" => $topArea["Album"]["name"], "class" => "thumbnail-small", "height" => 50)); ?>
                    <?php endif; ?>
                    <?php echo $this->Html->link($topArea["Album"]["Artist"]["name"], array('controller' => 'artists', 'action' => 'view', $topArea["Album"]["Artist"]["slug"])); ?> :
                    <?php echo $this->Html->link($topArea["Album"]["name"], array('controller' => 'albums', 'action' => 'view', $topArea["Album"]["slug"])); ?> :
                    <?php echo $this->Html->link($topArea["Track"]["title"], array('controller' => 'tracks', 'action' => 'view', $topArea["Track"]["slug"])); ?>
                    <p><?php echo __("Starts at") ?>: <?php echo date("i:s", $topArea["snapshot"]["start"]); ?> / <?php echo __("Ends at") ?> : <?php echo date("i:s", $topArea["snapshot"]["end"]); ?></p>
                   
                    <?php echo $this->Chart->getTrackChart($topArea["Track"]["slug"], $topArea["snapshot"]); ?>
                </li>
                <?php endif; endforeach; ?>
                </ul>
            <?php else : ?>
                <p><?php echo __("You have not reviewed anything yet."); ?></p>
            <?php endif; ?>
        </div>
        
    </div>
    
</div>
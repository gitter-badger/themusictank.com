
<?php echo $this->element('profilesmenu'); ?>

<p><?php echo $user["firstname"]; ?> <?php echo $user["lastname"]; ?> <?php echo __('has joined The Music Tank on'); ?> <?php echo $user['created']; ?></p>

<?php if($this->Session->read('Auth.User.User.id') != $user["id"]) : ?>
    <?php echo $this->element('followButton', array("relationExists" => $relationExists)); ?>
<?php endif;?>

<div class="cols">
    
    <div class="col col-1-2">        
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

    </div>    
    
    <div class="col col-2-2">

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
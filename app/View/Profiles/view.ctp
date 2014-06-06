<?php
    $currentUserId = $this->Session->read('Auth.User.User.id');
?>

<?php echo $this->element('profilesmenu'); ?>

<div class="container container-fluid">
    <div class="row">

        <div class="col-md-6">
            <h3><?php echo __("Recently reviewed"); ?></h3>
            <?php if(count($recentReviews) > 0) : ?>
                <section class="activity-stream">
                <?php foreach($recentReviews as $idx => $review) : ?>
                    <div class="event <?php echo ($idx % 2 === 0) ? 'right' : 'left'; ?>">


 							<div class="popoverbox review">
                                <div class="popover <?php echo ($idx % 2 === 0) ? 'right' : 'left'; ?>">
                                    <div class="arrow"></div>
                                    <h3 class="popover-title">
                                        <?php if($user["id"] === $currentUserId) : ?>
                                            <?php echo __("You have reviewed "); ?>
                                            <?php echo $this->Html->link(Hash::get($review, "Track.title"), array('controller' => 'tracks', 'action' => 'by_user', Hash::get($review, "Track.slug"), $user["slug"])); ?>
                                        <?php else : ?>
                                            <?php echo $this->Html->link($user["firstname"] . " " . $user["lastname"], array('controller' => 'profiles', 'action' => 'view', $user["slug"])); ?>
                                            <?php echo __(" has reviewed to "); ?>
                                            <?php echo $this->Html->link(Hash::get($review, "Track.title"), array('controller' => 'tracks', 'action' => 'by_user', Hash::get($review, "Track.slug"), $user["slug"])); ?>
                                        <?php endif; ?>
                                    </h3>
                                    <div class="popover-content">
                                            <?php echo $this->Html->link(
                                                    $this->Html->image($this->App->getImageUrl(Hash::get($review, "Album"), true), array("alt" => Hash::get($review, "Album.name"), "class" => "thumbnail")),
                                                    array('controller' => 'tracks', 'action' => 'view', Hash::get($review, "Track.slug")),
                                                    array('escape' => false)
                                            ); ?>
                                        <p>
                                            <?php echo $this->Html->link(Hash::get($review, "Track.title"), array('controller' => 'tracks', 'action' => 'view', Hash::get($review, "Track.slug"))); ?>
                                            <?php echo __("can be found on"); ?> <?php echo $this->Html->link(Hash::get($review, "Album.name"), array('controller' => 'albums', 'action' => 'view', Hash::get($review, "Album.slug"))); ?>
                                            <?php echo __("by"); ?> <?php echo $this->Html->link(Hash::get($review, "Artist.name"), array('controller' => 'artists', 'action' => 'view', Hash::get($review, "Artist.slug"))); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

<?php
/*


                        <div class="popoverbox review">
                            <div class="popover <?php echo ($idx % 2 === 0) ? 'right' : 'left'; ?>">
                                <div class="arrow"></div>


                                <h3 class="popover-title">
                                    <?php if($user["id"] === $userSessionId) : ?>
                                        <?php echo __("You have reviewed "); ?>

                                    <?php else : ?>
                                        <?php echo $this->Html->link($user["firstname"] . " " . $user["lastname"], array('controller' => 'profiles', 'action' => 'view', $user["slug"])); ?>
                                        <?php echo __(" has reviewed "); ?>
                                    <?php endif; ?>
                                    <?php echo $this->Html->link($review["Track"]["title"], array('controller' => 'tracks', 'action' => 'by_user', $review["Track"]["slug"], $user["slug"])); ?>
                                </h3>
                                <div class="popover-content">
                                    <?php if(!is_null($review["Album"]["image"])) : ?>
                                        <?php echo $this->Html->image($review["Album"]["image"], array("alt" => $review["Album"]["name"], "class" => "thumbnail-small", "height" => 50)); ?>
                                    <?php endif; ?>
                                    <?php echo $this->Html->link($review["Artist"]["name"], array('controller' => 'artists', 'action' => 'view', $review["Artist"]["slug"])); ?> :
                                    <?php echo $this->Html->link($review["Album"]["name"], array('controller' => 'albums', 'action' => 'view', $review["Album"]["slug"])); ?> :
                                    <?php echo $this->Html->link($review["Track"]["title"], array('controller' => 'tracks', 'action' => 'view', $review["Track"]["slug"])); ?>
                                    <?php echo $this->Chart->getSmallPie("usertrack", $review["Track"]["slug"], $review["appreciation"]); ?>
                                </div>
                            </div>
                        </div>
                        */ ?>


                    </div>
                <?php endforeach; ?>
                <div class="clearfix"></div>
                </section>
            <?php else : ?>
                <p><?php echo __("No recent activity."); ?></p>
            <?php endif; ?>
        </div>

        <div class="col-md-6"></div>

    </div>
</div>
<?php /*


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
</div> */ ?>

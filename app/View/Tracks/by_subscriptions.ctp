
<section>
    <p><?php echo sprintf(__("The following subscriptions have reviewed %s"), $track["title"]); ?></p>
<?php if(count($usersWhoReviewed) > 0) : ?>
    <?php foreach($usersWhoReviewed as $user) : ?>

        <div class="user">
            <div class="image">
                <?php if(array_key_exists("image", $user["User"]) && !is_null($user["User"]["image"]))
                            echo $this->Html->image($user["User"]["image"], array("alt" => $user["User"]["firstname"], "class" => "thumbnail", "height" => 150));
                ?>
            </div>
            <h4><?php echo $this->Html->link($user["User"]["firstname"] . " " . $user["User"]["lastname"], array('controller' => 'tracks', 'action' => 'by_user', $track["slug"], $user["User"]["slug"])); ?></h4>
        </div>

    <?php endforeach; ?>
<?php else : ?>
    <p><?php echo sprintf(__("None of the people you are subscribed to has reviewed %s."), $track["title"]); ?></p>
<?php endif; ?>
</section>
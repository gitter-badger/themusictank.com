<section class="follow-user">
    <p>
        <?php if($this->Session->read("Auth.User.User.id")) : ?>
            <?php if($relationExists) : ?>        
                <?php echo $this->Html->link("Unfollow", array('controller' => 'users', 'action' => 'unfollow', $user["slug"]), array("class" => "unfollow")); ?>
            <?php else : ?>
                <?php echo $this->Html->link("Follow", array('controller' => 'users', 'action' => 'follow', $user["slug"]), array("class" => "follow")); ?>
            <?php endif; ?>
        <?php else : ?>
            <?php echo $this->Html->link("Follow", array('controller' => 'users', 'action' => 'login', "?" => array("rurl" => "/users/view/" . $user["slug"] . "?follow=1"))); ?>
        <?php endif; ?>
    </p>
</section>
<?php 
if($this->Session->read('Auth.User.User.slug') != $user["slug"]) :  ?>
<section class="follow-user">
    <p>
        <?php if(!array_key_exists("currently_followed", $user)) : ?>
            <?php echo $this->Html->link("Follow", array('controller' => 'users', 'action' => 'login', "?" => array("rurl" => "/users/view/" . $user["slug"] . "?follow=1"))); ?>            
        <?php else : ?>
            <?php if($user["currently_followed"]) : ?>
                <?php echo $this->Html->link("Unfollow", array('controller' => 'users', 'action' => 'unfollow', $user["slug"]), array("class" => "unfollow")); ?>
            <?php else : ?>
                <?php echo $this->Html->link("Follow", array('controller' => 'users', 'action' => 'follow', $user["slug"]), array("class" => "follow")); ?>                
            <?php endif; ?>
        <?php endif; ?>
    </p>
</section>
<?php endif; ?>
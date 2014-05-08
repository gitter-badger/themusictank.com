<?php if($this->Session->read('Auth.User.User.slug') != $user["slug"]) :  ?>
    <?php if(!array_key_exists("currently_followed", $user)) : ?>
        <?php echo $this->Html->link("Follow", array('controller' => 'users', 'action' => 'login', "?" => array("rurl" => "/users/view/" . $user["slug"] . "?follow=1")), array("class" => "btn btn-success btn-lg btn-block")); ?>            
    <?php else : ?>
        <?php if($user["currently_followed"]) : ?>
            <?php echo $this->Html->link("Unfollow", array('controller' => 'users', 'action' => 'unfollow', $user["slug"]), array("class" => "unfollow btn btn-danger btn-lg btn-block")); ?>
        <?php else : ?>
            <?php echo $this->Html->link("Follow", array('controller' => 'users', 'action' => 'follow', $user["slug"]), array("class" => "follow btn btn-success btn-lg btn-block")); ?>                
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

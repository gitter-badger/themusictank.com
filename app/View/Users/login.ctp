<?php echo $this->Session->flash('auth'); ?>

<h2><?php echo __("Access your account"); ?></h2>

<h3><?php echo __("Authentication"); ?></h3>

<div class="cols">
    <div class="col col-1-2">    
        <ul>
            <li><?php echo $this->Html->link(__("Login with Rdio"), array('controller' => 'apis', 'action' => 'connectRdio', '?' => array("rurl" => $redirectUrl))); ?>
            <li><?php echo $this->Html->link(__("Login with Facebook"), array('controller' => 'apis', 'action' => 'connectFacebook', '?' => array("rurl" => $redirectUrl))); ?></p>        
        </ul>
    </div>
    <div class="col col-2-2">        
        <p><?php echo __("Connecting The Music Tank with Rdio or Facebook will automatically give you access to this website. If you have accounts on these, you have an account on TMT."); ?></p>
    </div>            
</div>        

<h3><?php echo __("If you do not use any of the above..."); ?></h3>

<div class="cols">
    <div class="col col-1-2">                  
        <?php echo $this->Form->create('User', array('action' => 'login')); ?>
            <p><?php echo __("Connect with your existing TMT account"); ?></p>      
            <?php echo $this->Form->input('username', array("label" => __("Email"))); ?>
            <?php echo $this->Form->input('password'); ?>
        <?php echo $this->Form->end(__('Login')); ?>
    </div>

    <div class="col col-2-2">                
        <p><?php echo $this->Html->link("Create a new TMT account", array('controller' => 'users', 'action' => 'create', '?' => array("rurl" => $redirectUrl))); ?></p>
    </div>            
</div>
<h2><?php echo __("Edit your profile"); ?></h2>

<?php echo $this->Form->create(); ?>

<?php echo $this->Form->input("id"); ?>

<p><?php echo __("Modify your personal TMT url"); ?></p>
<?php echo $this->Form->input("slug", array("label" => "http://www.themusictank.com/users/view/")); ?>


<h3><?php echo __("Preferences"); ?></h3>

<p><?php echo __("When available, we will use your prefered player to play songs. When your choice is not available, we will give you the option to use an mp3 file."); ?></p>
<?php echo $this->Form->input("preferred_player_api", array("options" => $availableApis)); ?>

<h3><?php echo __("Additional information"); ?></h3>
<p><?php echo __("You don't have to fill the following to review songs and albums on The Music Tank, but it does make this place a little more lively if you take the time to describe yourself."); ?></p>

<?php echo $this->Form->input("firstname"); ?>
<?php echo $this->Form->input("lastname"); ?>
<?php echo $this->Form->input("location"); ?>

<?php echo $this->Form->end(__("Update")); ?>
    
<h2><?php echo __("Manage third party services"); ?></h2>

<ul>
    <li>
        <?php if($hasRdio) : ?>        
            <?php if(!$hasFacebook && !$hasAccount) : ?>
                <?php echo __("Rdio being your only active authentification method, we cannot revoke it."); ?>
            <?php else : ?>
                <?php echo $this->Html->link("Disconnect your Rdio account", array('controller' => 'users', 'action' => 'disconnectRdio')); ?>
            <?php endif; ?>
        <?php else : ?>
            <?php echo $this->Html->link("Connect a Rdio account", array('controller' => 'apis', 'action' => 'connectRdio', '?' => array("rurl" => "/users/edit"))); ?>
        <?php endif; ?>
    </li>
    <li>
        <?php if($hasFacebook) : ?>                   
            <?php if(!$hasRdio && !$hasAccount) : ?>
                <?php echo __("Facebook being your only active authentification method, we cannot revoke it."); ?>
            <?php else : ?>
                <?php echo $this->Html->link("Disconnect your Facebook account", array('controller' => 'users', 'action' => 'disconnectFacebook')); ?>
            <?php endif; ?>
        <?php else : ?>
            <?php echo $this->Html->link("Connect a Facebook account", array('controller' => 'apis', 'action' => 'connectFacebook')); ?>
        <?php endif; ?>
    </li>
</ul>
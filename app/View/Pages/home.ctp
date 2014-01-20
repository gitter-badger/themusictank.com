<div class="hero">
    <h1><?php echo __("Welcome to The Music Tank"); ?></h1>
    <h2><?php echo __("A brand new way of sharing your thoughts on music."); ?></h2>
</div>

<div class="create-account">
    <p><?php echo __("Using your personal mp3 collection or by connecting your Rdio account, The Music Tank's reviewer will precisely take note of your enjoyement throughout tracks."); ?></p>
    <p><?php echo $this->Html->link( __("Start grooving!"), array('controller' => 'users', 'action' => 'login')); ?></p>
</div>

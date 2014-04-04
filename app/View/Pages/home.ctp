<section class="hero">
    <h2><?php echo __("A new way of sharing your thoughts on music."); ?></h2>
    <a href="#how-does-it-work"><?php echo __("How does it work?"); ?></a>        
</section>

<section class="create-account">
    <p><?php echo __("Using your personal mp3 collection or by connecting your Rdio account, The Music Tank's reviewer will precisely take note of your enjoyement throughout tracks."); ?></p>
    <p><?php echo $this->Html->link( __("Start grooving!"), array('controller' => 'users', 'action' => 'login')); ?></p>
</section>

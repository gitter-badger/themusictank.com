<footer>        
    <ul class="horizontal">            
        <li><?php echo $this->Html->link(__("Artists list"), array('controller' => 'artists', 'action' => 'index')); ?></li>  
        <li><?php echo $this->Html->link(__("About"), array('controller' => 'pages', 'action' => 'about')); ?></li>   
        <li><?php echo $this->Html->link(__("Legal"), array('controller' => 'pages', 'action' => 'legal')); ?></li>
        
        <li>---</li>
        
        <li><?php echo $this->Html->link(__("Community"), array('controller' => 'pages', 'action' => 'community')); ?></li>   
        <li><?php echo $this->Html->link(__("Facebook"), "http://www.facebook.com/themusictank/", array("target" => "_blank")); ?></li>
        <li><?php echo $this->Html->link(__("Twitter"), "http://www.twitter.com/themusictank/", array("target" => "_blank")); ?></li>
        <li><?php echo $this->Html->link(__("Google+"), "https://plus.google.com/117543200043480372792", array("target" => "_blank", "rel" => "publisher")); ?></li>
    </ul>

    &copy; 1999 - <?php echo date("Y"); ?> <?php echo __("The Music Tank"); ?>
</footer>
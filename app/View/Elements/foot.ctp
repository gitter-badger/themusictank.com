<footer class="site-foot">      
    <nav class="social">
        <ul class="horizontal">        
            <li class="facebook"><?php echo $this->Html->link(__("Facebook"), "http://www.facebook.com/themusictank/", array("target" => "_blank")); ?></li>
            <li class="twitter"><?php echo $this->Html->link(__("Twitter"), "http://www.twitter.com/themusictank/", array("target" => "_blank")); ?></li>
            <li class="googleplus"><?php echo $this->Html->link(__("Google+"), "https://plus.google.com/117543200043480372792", array("target" => "_blank", "rel" => "publisher")); ?></li>
        </ul>
    </nav>
    <nav class="website">
        <ul class="horizontal">
            <li><?php echo $this->Html->link(__("About"), array('controller' => 'pages', 'action' => 'about')); ?></li>   
            <li><?php echo $this->Html->link(__("Legal"), array('controller' => 'pages', 'action' => 'legal')); ?></li>
            <li class="copyright">
                &copy; 1999 - <?php echo date("Y"); ?> <?php echo __("The Music Tank"); ?>
            </li>
        </ul>
    </nav>
</footer>
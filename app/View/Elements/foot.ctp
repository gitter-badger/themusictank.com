<footer class="site-foot">  
    <nav>
        <ul class="nav nav-pills social">        
            <li><i class="fa fa-facebook"></i> <?php echo $this->Html->link(__("Facebook"), "http://www.facebook.com/themusictank/", array("target" => "_blank")); ?></li>
            <li><i class="fa fa-twitter"></i> <?php echo $this->Html->link(__("Twitter"), "http://www.twitter.com/themusictank/", array("target" => "_blank")); ?></li>
            <li><i class="fa fa-google-plus"></i> <?php echo $this->Html->link(__("Google+"), "https://plus.google.com/117543200043480372792", array("target" => "_blank", "rel" => "publisher")); ?></li>
        </ul>
        <ul class="nav nav-pills website">   
            <li><?php echo $this->Html->link(__("About"), array('controller' => 'pages', 'action' => 'about')); ?></li>   
            <li><?php echo $this->Html->link(__("Legal"), array('controller' => 'pages', 'action' => 'legal')); ?></li>
            <li class="copyright">
                &copy; 1999 - <?php echo date("Y"); ?> <?php echo __("The Music Tank"); ?>
            </li>
        </ul>
    </nav>
</footer>
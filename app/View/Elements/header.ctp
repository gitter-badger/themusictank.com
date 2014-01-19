<header class="cols">
     <nav class="col col-1-2">
         <ul class="horizontal">
             <li><?php echo $this->Html->link(__("The Music Tank"), "/"); ?></li>
             <!--li><?php echo $this->Html->link(__("Charts"),     array('controller' => 'charts',     'action' => 'index')); ?></li -->
             <li><?php echo $this->Html->link(__("Artists"),    array('controller' => 'artists',    'action' => 'index')); ?></li>
             <li><?php echo $this->Html->link(__("Community"),  array('controller' => 'pages',      'action' => 'community')); ?></li>
         </ul>
    </nav>
    <nav class="col col-2-2">
         <?php $userSession = $this->Session->read('Auth.User');  ?>
         <?php if($userSession) : ?>
         <ul class="horizontal">             
            <li>
                <?php echo $this->Html->link(
                        array_key_exists("image", $userSession) && !is_null($userSession["image"]) ? 
                            $this->Html->image($userSession["image"], array("alt" => $userSession["firstname"], "class" => "thumbnail", "height" => 50)) 
                            : $userSession["firstname"]
                        ,
                        array('controller' => 'users', 'action' => 'dashboard'),
                        array("escape" => false)
                ); ?>
                <?php echo __("Hi"); ?> <?php echo $this->Html->link($userSession['firstname'], array('controller' => 'users', 'action' => 'dashboard')); ?>
            </li>
            <li class="dropdown notifier"><?php echo $this->element("dropdownnotifications"); ?></li>
            <li><?php echo $this->Html->link(__("Logout"), array('controller' => 'users', 'action' => 'logout')); ?></li>             
        </ul>
        <?php else : ?>
         <ul class="horizontal">             
            <li><?php echo $this->Html->link(__("Login"), array('controller' => 'users', 'action' => 'login')); ?></li>
            <li><?php echo $this->Html->link(__("Create account"), array('controller' => 'users', 'action' => 'login')); ?></li>
        </ul>
        <?php endif; ?>
    </nav>
 </header>
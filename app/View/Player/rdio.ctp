<?php 
    $userSession = $this->Session->read('Auth.User.User');  
?>
<header>
   <?php echo $this->Html->link(__("The Music Tank"), "/", array("class" => "navbar-brand", "title" => __("The Music Tank"))); ?>
  	<ul class="nav navbar-nav navbar-right">
        <li class="picture">
            <?php echo $this->Html->link(
                    $this->Html->image($this->App->getImageUrl($userSession), array("class" => "img-circle")),
                    array('controller' => 'users', 'action' => 'dashboard'),
                    array('escape' => false)
            ); ?>
        </li>
    </ul>
</header>    
<div class="simpler container fluid-container">	
	<?php echo $this->element("reviewer"); ?>
</div>
<?php 
    $userSession = $this->Session->read('Auth.User.User');  
    $isLogged = $this->Session->check('Auth.User.User.id');  
?>
  <div class="navbar navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <?php echo $this->Html->link(__("The Music Tank"), "/", array("class" => "navbar-brand", "title" => __("The Music Tank"))); ?>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><?php echo $this->Html->link(__("Artists"), array('controller' => 'artists', 'action' => 'index')); ?></li>
                <li><?php echo $this->Html->link(__("Community"), array('controller' => 'pages', 'action' => 'community')); ?></li>
            </ul> 

            <ul class="nav navbar-nav navbar-right">
                <?php if($isLogged) : ?>
                    <li class="picture">
                        <?php echo $this->Html->link(
                                $this->Html->image($this->App->getImageUrl($userSession), array("class" => "img-circle")),
                                array('controller' => 'users', 'action' => 'dashboard'),
                                array('escape' => false)
                        ); ?>
                    </li>
                    <li> 
                        <?php echo $this->Html->link("<i class=\"fa fa-bell-o\"></i>", array('controller' => 'users', 'action' => 'notifications'), array("escape" => false)); ?>
                        <?php //echo $this->element("dropdownnotifications"); ?>
                    </li>
                    <li><?php echo $this->Html->link(__("Profile"), array('controller' => 'profiles', 'action' => 'view', $userSession['slug'])); ?></li>
                    <li class="dropdown">
                        <button type="button" id="btnSettings" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                          <i class="fa fa-cog"></i>
                          <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="btnSettings">
                            <li><?php echo $this->Html->link(__("Settings"), array('controller' => 'users', 'action' => 'edit')); ?></li>
                            <li><?php echo $this->Html->link(__("Logout"), array('controller' => 'users', 'action' => 'logout')); ?></li>        
                        </ul>
                    </li>
                <?php else : ?>
                    <li><?php echo $this->Html->link(__("Login or create account"), array('controller' => 'users', 'action' => 'login')); ?></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<section class="search fixable">
    <div class="container">
        <form action="/search/" method="get">        
            <input class="typeahead" type="text" name="q" value="" placeholder="Search across everything" />
            <input type="submit" />
        </form>
    </div>
</section>
<?php 
    $userSession = $this->Session->read('Auth.User.User');  
    $isLogged = $this->Session->check('Auth.User.User.id');  
?>
<header class="fixable site-head">
    
    <h1><?php echo $this->Html->link(__("The Music Tank"), "/", array("title" => __("The Music Tank"))); ?></h1>

    <nav class="account">
        <ul>
            <?php if(!$isLogged) : ?>
            <li class="login"><?php echo $this->Html->link(__("Login or create account"), array('controller' => 'users', 'action' => 'login')); ?></li>
            <?php else : ?>
                <li class="picture">
                    <?php echo $this->Html->link(
                            $this->Html->image($this->App->getImageUrl($userSession)),
                            array('controller' => 'users', 'action' => 'profile', $userSession["slug"]),
                            array('escape' => false)
                    ); ?>
                </li>                
                <li><?php echo $this->Html->link(__("Home"), array('controller' => 'users', 'action' => 'dashboard')); ?></li>
                <li><?php echo $this->Html->link(__("Profile"), array('controller' => 'profiles', 'action' => 'view', $userSession['slug'])); ?></li>
                <li>
                    <button type="button" id="btnSettings" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                      Dropdown
                      <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="btnSettings">
                        <li><?php echo $this->Html->link(__("Settings"), array('controller' => 'users', 'action' => 'edit')); ?></li>
                        <li><?php echo $this->Html->link(__("Logout"), array('controller' => 'users', 'action' => 'logout')); ?></li>        
                    </ul>
                </li>

            <?php endif; ?>
        </ul>
    </nav>
 </header>

 <section class="search">
    <form action="/search/" method="get">
        <i class="fa fa-search"></i>
        <input class="typeahead tt-input" type="text" name="q" value="" placeholder="<?php echo __("Search"); ?>..." />
        <input type="submit" />
    </form>
</section>
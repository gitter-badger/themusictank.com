<!DOCTYPE html>
<html>
<head>
    <?php $this->MetaTags->init(); ?>
    <?php if(isset($meta)) $this->MetaTags->addPageMeta($meta); ?>
   	<?= $this->MetaTags->compileMetas();  ?>
   	<?= $this->fetch('meta') ?>
	<?= $this->fetch('css') ?>
</head>
<body class="<?= $this->Tmt->contextToClassNames(); ?>">
    <div class="website">
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
                        <li class="searchbar">
                            <form action="/search/" method="get">
                                <input class="typeahead" type="text" name="q" value="" placeholder="Search across everything" />
                                <input type="submit" />
                            </form>
                        </li>
                        <li><?php echo $this->Html->link(__("Artists"), ['controller' => 'artists', 'action' => 'index']); ?></li>
                        <li><?php echo $this->Html->link(__("Community"), ['controller' => 'pages', 'action' => 'community']); ?></li>
                        <?php if(isset($userSession)) : ?>
                            <li class="picture">
                                <?php echo $this->Html->link(
                                        $this->Html->image($userSession->getImageUrl(), ["class" => "img-circle"]),
                                        ['controller' => 'profiles', 'action' => 'dashboard'],
                                        ['escape' => false]
                                ); ?>
                            </li>
                            <li><?php echo $this->Html->link(__("You"), ['controller' => 'profiles', 'action' => 'view', $userSession->slug]); ?></li>
                            <li><?php echo $this->Html->link("<i class=\"fa fa-bell-o\"></i>", ['controller' => 'notifications', 'action' => 'index'], ["escape" => false]); ?></li>
                            <li class="dropdown">
                                <a href="#" id="btnSettings" class="dropdown-toggle" data-toggle="dropdown">
                                  <i class="fa fa-cog"></i>
                                  <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="btnSettings">
                                    <li><?php echo $this->Html->link(__("Settings"), ['controller' => 'users', 'action' => 'edit']); ?></li>
                                    <li><?php echo $this->Html->link(__("Logout"), ['controller' => 'users', 'action' => 'logout']); ?></li>
                                </ul>
                            </li>
                            <?php if($userSession->isAdmin()) : ?>
                                <li class="dropdown">
                                    <a href="#" id="btnAdmin" class="dropdown-toggle" data-toggle="dropdown">
                                      <i class="fa fa-sliders"></i>
                                      <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu" role="menu" aria-labelledby="btnAdmin">
                                        <li><?php echo $this->Html->link(__("Console"), ['controller' => 'tmt', 'action' => 'index']); ?></li>
                                        <li><?php echo $this->Html->link(__("Bugs"), ['controller' => 'tmt', 'action' => 'bugs']); ?></li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        <?php else : ?>
                            <li><?php echo $this->Html->link(__("Your account"), ['controller' => 'profiles', 'action' => 'dashboard']); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <?= $this->fetch('content'); ?>

        <footer class="site-foot">
            <div class="container">
                <nav>
                    <ul class="nav nav-pills social">
                        <li><?php echo $this->Html->link('<i class="fa fa-facebook"></i> ' . __("Facebook"), "http://www.facebook.com/themusictank/", array("target" => "_blank", "escape" => false)); ?></li>
                        <li><?php echo $this->Html->link('<i class="fa fa-twitter"></i> ' . __("Twitter"), "http://www.twitter.com/themusictank/", array("target" => "_blank", "escape" => false)); ?></li>
                        <li><?php echo $this->Html->link('<i class="fa fa-google-plus"></i> ' . __("Google+"), "https://plus.google.com/117543200043480372792", array("target" => "_blank", "escape" => false)); ?></li>
                    </ul>
                    <ul class="nav nav-pills website">
                        <li><?php echo $this->Html->link(__("About"), array('controller' => 'pages', 'action' => 'about')); ?></li>
                        <li><?php echo $this->Html->link(__("Legal"), array('controller' => 'pages', 'action' => 'legal')); ?></li>
                        <li class="copyright">
                            1999 - <?php echo date("Y"); ?> <?php echo __("The Music Tank"); ?>
                        </li>
                    </ul>
                </nav>
            </div>
        </footer>
    </div>
<?= $this->MetaTags->compileScripts();  ?>
<?= $this->fetch('script') ?>
<?= $this->fetch('bottom-extra') ?>
<?= $this->element('analytics'); ?>
</body>
</html>

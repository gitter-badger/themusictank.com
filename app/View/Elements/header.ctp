<?php
	$isLogged = $this->Session->check('Auth.User.User.id');
	$userSession = $this->Session->read('Auth.User.User');
	$userRole = $this->Session->read('Auth.User.User.role');
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
				<li class="searchbar">
					<form action="/search/" method="get">
						<input class="typeahead" type="text" name="q" value="" placeholder="Search across everything" />
						<input type="submit" />
					</form>
				</li>
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
					<li><?php echo $this->Html->link("<i class=\"fa fa-bell-o\"></i>", array('controller' => 'users', 'action' => 'notifications'), array("escape" => false)); ?></li>
					<li><?php echo $this->Html->link(__("Profile"), array('controller' => 'profiles', 'action' => 'view', $userSession['slug'])); ?></li>
					<li class="dropdown">
						<a href="#" id="btnSettings" class="dropdown-toggle" data-toggle="dropdown">
						  <i class="fa fa-cog"></i>
						  <span class="caret"></span>
						</a>
						<ul class="dropdown-menu" role="menu" aria-labelledby="btnSettings">
							<li><?php echo $this->Html->link(__("Settings"), array('controller' => 'users', 'action' => 'edit')); ?></li>
							<li><?php echo $this->Html->link(__("Logout"), array('controller' => 'users', 'action' => 'logout')); ?></li>
						</ul>
					</li>
					<?php if($userRole === "admin") : ?>
						<li class="dropdown">
							<a href="#" id="btnAdmin" class="dropdown-toggle" data-toggle="dropdown">
							  <i class="fa fa-sliders"></i>
							  <span class="caret"></span>
							</a>
							<ul class="dropdown-menu" role="menu" aria-labelledby="btnAdmin">
								<li><?php echo $this->Html->link(__("Console"), array('controller' => 'tmt', 'action' => 'index')); ?></li>
								<li><?php echo $this->Html->link(__("Bugs"), array('controller' => 'tmt', 'action' => 'bugs')); ?></li>
							</ul>
						</li>
					<?php endif; ?>
				<?php else : ?>
					<li><?php echo $this->Html->link(__("Login or create account"), array('controller' => 'users', 'action' => 'login')); ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>

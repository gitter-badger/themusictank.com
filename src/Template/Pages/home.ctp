<div class="header-wrapper">
	<section class="jumbotron introduction">
		<div class="container">
		    <h1><?php echo __("Collaborative Reviews"); ?></h1>
		    <p class="lead"><?php echo __("The Music Tank is a place where opiniated people share their thoughts on music."); ?></p>

		    <ul>
    			<li><a href="#how-does-it-work"><?php echo __("How does it work?"); ?></a></li>
			</ul>
	    </div>
	</section>
</div>

<div class="container container-fluid">
	<div class="row">
		<section id="how-does-it-work" class="col-md-6">
			<h2><?php echo __("A new way of sharing your thoughts on music."); ?></h2>
		</section>
	</div>
</div>

<div class="container container-fluid">
	<div class="row">
		<section class="col-md-6 create-account">
		    <p><?php echo $this->Html->link( __("Start grooving!"), array('controller' => 'users', 'action' => 'login')); ?></p>
		</section>
	</div>
</div>

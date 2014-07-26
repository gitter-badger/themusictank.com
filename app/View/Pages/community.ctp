<div class="header-wrapper">
	<section class="jumbotron introduction">
		<div class="container">
		    <h1><?php echo __("Community hub"); ?></h1>
	    </div>
	</section>
</div>

<div class="container container-fluid">
	<div class="row">
		<div class="col-md-12">
			<?php echo $this->Disqus->get('/pages/community/', __("Community")); ?>
		</div>
	</div>
</div>

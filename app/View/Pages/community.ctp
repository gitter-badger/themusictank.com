<div class="header-wrapper plain">
    <i class="mask"></i>
</div>

<article class="container container-fluid static">

    <header class="collapsed"></header>

    <div class="row content headerless">

	    <h1><?php echo __("Community hub"); ?></h1>
		<div class="col-md-12">
			<?php echo $this->Disqus->get('/pages/community/', __("Community")); ?>
		</div>

	</div>

</article>


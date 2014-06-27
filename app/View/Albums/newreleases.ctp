
<div class="header-wrapper">
	<section class="jumbotron introduction">
		<div class="container">
		    <h1><?php echo __("New album releases"); ?></h1>
        	<p class="lead"><?php echo __("These are the most recent albums added to our list.") ?></p>
	    </div>
	</section>
</div>

<nav class="sub-menu">
	<div class="container container-fluid">
		<div class="row">
		    <ol class="breadcrumb">
		        <li class="active"><?php echo $this->Html->link(__("Artists"), array('controller' => 'artists', 'action' => 'index')); ?></li>
		    </ol>
	    </div>
    </div>
</nav>

<div class="container container-fluid">
    <section class="row new-album-releases">
        <?php echo $this->element('albumTiledList', array("albums" => $newReleases)); ?>
    </section>
</div>

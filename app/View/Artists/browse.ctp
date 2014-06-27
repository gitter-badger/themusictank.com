
<div class="header-wrapper">
	<section class="jumbotron introduction">
		<div class="container">
		    <h1><?php echo __("Artist search"); ?></h1>
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
	<div class="row">
		<h2><?php echo $title; ?></h2>
		<?php echo $this->element('artistTiledList', array("artists" => $artists)); ?>

		 <ul class="pagination">
		    <?php
		        echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
		        echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
		        echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
		    ?>
		</ul>
    </div>
</div>

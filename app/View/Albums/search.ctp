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
		<h2><?php echo __("Albums"); ?></h2>
		<h3><?php echo $title; ?></h3>

		<?php if(count($albums) > 0): ?>
			<?php echo $this->element('albumTiledList', array("albums" => $albums)); ?>
		<?php else : ?>
			<p><?php echo __("Search returned no results."); ?></p>
		<?php endif; ?>

		 <ul class="pagination">
		    <?php
		        echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
		        echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
		        echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
		    ?>
		</ul>
    </div>
</div>

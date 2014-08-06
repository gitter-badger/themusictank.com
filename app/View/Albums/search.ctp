<div class="header-wrapper plain">
    <i class="mask"></i>
</div>

<article class="container container-fluid static">

    <header class="collapsed"></header>

    <div class="row content headerless">

	    <h1><?php echo __("Album search"); ?></h1>
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
</article>

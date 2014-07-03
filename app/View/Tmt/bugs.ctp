<?php echo $this->element('header'); ?>

<div class="header-wrapper">
	<section class="jumbotron introduction">
		<div class="container">
		    <h1><?php echo __("Bugs"); ?></h1>
	    </div>
	</section>
</div>

<div class="container container-fluid">

	<div class="row">
		<table class="table table-hover">
			<thead>
				<th>Reporter</th>
				<th>Reported</th>
				<th>Fixed?</th>
				<th>Type</th>
				<th>Location</th>
				<th>Details</th>
				<th>Action</th>
			</thead>
			<tbody>
			<?php foreach($bugs as $bug) : ?>
				<tr>
					<td>
						<?php  echo $this->Html->link(
			                $this->Html->image($this->App->getImageUrl($bug["User"]), array("class" => "img-circle", "width" => 50, "alt" => $bug["User"]["firstname"] . " " . $bug["User"]["lastname"], "title" => $bug["User"]["firstname"] . " " . $bug["User"]["lastname"])),
			                array('controller' => 'profiles', 'action' => 'view', $bug["User"]["slug"]),
			                array('escape' => false)
			        	); ?>
						<?php echo $bug["User"]["firstname"] . " " . $bug["User"]["lastname"]; ?>
					</td>
					<td><?php echo $this->Time->timeAgoInWords($bug["Bug"]["created"]); ?></td>
					<td>
						<span class="label label-<?php echo (bool)$bug["Bug"]["is_fixed"] ? "success" : "warning"; ?>"><?php echo (bool)$bug["Bug"]["is_fixed"] ? "FIXED" : "PENDING"; ?></span>
					</td>
					<td><?php echo $bug["Bug"]["type"]; ?></td>
					<td><?php echo $bug["Bug"]["location"]; ?></td>
					<td><?php echo $bug["Bug"]["details"]; ?></td>
					<td><input type="checkbox" name="is_fixed" value="true" data-bug-id="<?php echo (int)$bug["Bug"]["id"]; ?>" <?php if((bool)$bug["Bug"]["is_fixed"]) : ?>checked="checked"<?php endif; ?> /></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		 <ul class="pagination">
		    <?php
		        echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
		        echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
		        echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
		    ?>
		</ul>
    </div>
</div>

<script>
	$("table input[type=checkbox]").click(function(){
		var el = $(this);
		$.post("/tmt/bugstatus", {id: parseInt(el.attr("data-bug-id"), 10), is_fixed: this.checked ? 1 : 0 });
	});
</script>

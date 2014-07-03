<?php echo $this->element('header'); ?>

<div class="header-wrapper">
	<section class="jumbotron introduction">
		<div class="container">
		    <h1><?php echo __("Console"); ?></h1>
	    </div>
	</section>
</div>

<div class="container container-fluid">
	<div class="row">
		<div id="refresher" class="loading-wrap">
        	<i class="fa fa-refresh fa-spin fa-fw"></i>
        </div>
	</div>
</div>

<style type="text/css">
	.log {
		min-height:300px;
	}
	.log pre {
		height:300px;
		overflow:auto;
		background:#000;
		color:#eee;
		font-family: consolas, sans;
		border-radius: 0;
	}
</style>
<script>
	$(function(){
		function refreshConsole() {
			$("#refresher").load("/tmt/sync", function() {
				$(".log pre").each(function(idx){
					this.scrollTop = this.scrollHeight;
				});
			});
			setTimeout(refreshConsole, 60 * 1000);
		}
		refreshConsole();
	});
</script>

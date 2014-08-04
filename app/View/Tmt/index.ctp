<?php echo $this->element('header'); ?>

<div class="header-wrapper plain">
    <i class="mask"></i>
</div>

<article class="container container-fluid static">

    <header class="collapsed"></header>

    <div class="row content headerless">
        <h1><?php echo __("Console"); ?></h1>

		<div id="refresher" class="loading-wrap">
        	<i class="fa fa-refresh fa-spin fa-fw"></i>
        </div>
	</div>
</article>


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

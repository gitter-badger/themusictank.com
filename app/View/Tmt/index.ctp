<div class="container container-fluid">

	<h1>Console</h1>
	<div id="refresher">Loading...</div>

</div>
<style type="text/css">
	.log pre {
		max-height:300px;
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
		}
		refreshConsole();
		setTimeout(refreshConsole, 60 * 1000);
	});
</script>

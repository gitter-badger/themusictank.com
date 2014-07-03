<div class="row">

	<div class="col-md-6">
		<h3>Stats</h3>
		<div class="row">
			<div class="col-md-5 col-md-offset-1">
				<h2><?php echo $userCount; ?></h2>
				<p>Users</p>
			</div>

			<div class="col-md-5 col-md-offset-1">
				<h2><?php echo $artistCount; ?></h2>
				<p>Artists</p>
			</div>

			<div class="col-md-5 col-md-offset-1">
				<h2><?php echo $albumCount; ?></h2>
				<p>Albums</p>
			</div>

			<div class="col-md-5 col-md-offset-1">
				<h2><?php echo $trackCount; ?></h2>
				<p>Tracks</p>
			</div>

			<div class="col-md-5 col-md-offset-1">
				<h2><?php echo $bugCount; ?></h2>
				<p>Opened issues</p>
			</div>

			<div class="col-md-5 col-md-offset-1">

			</div>
		</div>
	</div>

	<div class="col-md-6">
		<h3>Config</h3>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Key</th>
					<th>Value</th>
				</tr>
			</thead>
			<tbody>
			<?php $keys = Hash::extract($configs, "{n}.Config.key"); ?>
			<?php $values = Hash::extract($configs, "{n}.Config.value"); ?>
			<?php foreach($keys as $idx => $row) : ?>
				<tr>
					<td><?php echo $keys[$idx]; ?></td>
					<td>
						<?php if(!is_null($values[$idx])) : ?>
							<?php echo date("F j, Y, g:i a", $values[$idx]); ?>
						<?php else : ?>
							Null
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="row">

	<div class="col-md-12">

		<ul class="nav nav-tabs" role="tablist">
		  <li class="active"><a href="#error" role="tab" data-toggle="tab">Error log</a></li>
		  <li><a href="#debug" role="tab" data-toggle="tab">Debug log</a></li>
		</ul>

		<div class="tab-content">
		  <div class="tab-pane active" id="error"><div class="log"><pre><?php echo $logError; ?></pre></div></div>
		  <div class="tab-pane" id="debug"><div class="log"><pre><?php echo $logDebug; ?></pre></div></div>
		</div>

	</div>

</div>

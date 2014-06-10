<?php

class CronShell extends AppShell {

	public $uses = array('Config');

	// /Applications/MAMP/bin/php/php5.4.10/bin/php app/Console/cake.php Cron weekly themusictank.nvi
	public function daily()
	{
		$this->out("Updating daily tasks");
        $this->Config->updateCachedDaily();
	}

	public function weekly()
	{
		$this->out("Updating daily tasks");
		$this->out("Syncing track review snapshots");
        $this->Config->updateCachedWeekly();
	}
}

<?php
class UpdatePopularArtistsTask extends Shell {

    public $uses = array('Config');

    public function execute()
    {
		$this->out("Updating popular artists...");
		$this->Config->updatePopularArtists();
		$this->out("Completed.");
    }
}

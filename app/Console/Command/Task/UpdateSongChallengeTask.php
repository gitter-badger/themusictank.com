<?php
class UpdateSongChallengeTask extends Shell {

    public $uses = array('Config');

    public function execute()
    {
		$this->out("Updating daily song challenge...");
		$this->Config->updateTrackChallenge();
		$this->out("Completed.");
    }
}

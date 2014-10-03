<?php
class UpdateSongChallengeTask extends Shell {

    public $uses = array('Config');

    public function execute()
    {
        $this->out("Updating <comment>daily song challenge</comment>...");
        $this->Config->updateTrackChallenge();
        $this->out("\t<info>Completed</info>");
    }
}

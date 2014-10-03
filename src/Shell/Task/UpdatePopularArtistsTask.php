<?php
class UpdatePopularArtistsTask extends Shell {

    public $uses = array('Config');

    public function execute()
    {
        $this->out("Updating <comment>popular artists</comment>...");
        $this->Config->updatePopularArtists();
        $this->out("\t<info>Completed.</info>");
    }
}

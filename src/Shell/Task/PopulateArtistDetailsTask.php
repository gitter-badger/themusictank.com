<?php
class PopulateArtistDetailsTask extends Shell {

    public $uses = array('LastfmArtist');

    public function execute()
    {
        $expired = $this->LastfmArtist->find("all", array(
            "conditions" => array(
                "or" => array(
                    "lastsync IS NULL",
                    "lastsync < " . $this->LastfmArtist->getExpiredRange()
                )
            ),
            "limit" => 200 // I think it's better to do a few of them at the time.
        ));

        $this->out(sprintf("Found <comment>%s artist</comment> that are out of sync.", count($expired)));
        foreach ($expired as $artist) {
            $this->LastfmArtist->data = $artist;
            $this->out(sprintf("\t<info>%d\t%s</info>", $this->LastfmArtist->getData("Artist.id"), $this->LastfmArtist->getData("Artist.name")));
            $this->LastfmArtist->updateCached();
        }
    }
}

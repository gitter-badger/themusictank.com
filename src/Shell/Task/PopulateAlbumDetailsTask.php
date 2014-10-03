<?php
class PopulateAlbumDetailsTask extends Shell {

    public $uses = array('LastfmAlbum');

    public function execute()
    {
        // Start with expired albums
        $expired = $this->LastfmAlbum->Album->find("all", array(
            "conditions" => array(
                "or" => array(
                    "LastfmAlbum.lastsync IS NULL",
                    "LastfmAlbum.lastsync < " . $this->LastfmAlbum->getExpiredRange()
                )
            ),
            "fields"    => array("Album.*", "Artist.*", "LastfmAlbum.*"),
            "limit" => 200 // I think it's better to do a few of them at the time.
        ));

        $this->out(sprintf("Found <comment>%s albums</comment> that are out of sync.", count($expired)));
        foreach ($expired as $album) {
            $this->LastfmAlbum->data = $album;
            $this->out(sprintf("\t<info>%d\t%s</info>", $this->LastfmAlbum->getData("Album.id"), $this->LastfmAlbum->getData("Album.name")));
            $this->LastfmAlbum->updateCached();
        }
    }
}

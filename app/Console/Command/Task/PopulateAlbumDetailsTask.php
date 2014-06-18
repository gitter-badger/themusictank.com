<?php
class PopulateAlbumDetailsTask extends Shell {

	public $uses = array('LastfmAlbum');

	public function execute()
	{
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
    		$this->out(sprintf("\t<info>%s (%d)</info>", $this->LastfmAlbum->getData("Album.name"), $this->LastfmAlbum->getData("Album.id")));
    		$this->LastfmAlbum->updateCached();
    	}
	}
}

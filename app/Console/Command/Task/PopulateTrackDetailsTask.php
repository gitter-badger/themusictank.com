<?php
class PopulateTrackDetailsTask extends Shell {

	public $uses = array('LastfmTrack');

	public function execute()
	{
		$expired = $this->LastfmTrack->Track->find("all", array(
			"conditions" => array(
				"or" => array(
					"LastfmTrack.lastsync IS NULL",
					"LastfmTrack.lastsync < " . $this->LastfmTrack->getExpiredRange()
				)
			),
            "contain" 		=> array("LastfmTrack", "Album" => array("Artist")),
            "limit"			=> 400
        ));

		$this->out(sprintf("Found <comment>%s tracks</comment> that are out of sync.", count($expired)));
		foreach ($expired as $track) {
			$this->LastfmTrack->data = $track;
			$this->LastfmTrack->data["Album"] = $track["Album"];
			$this->LastfmTrack->data["Artist"] = $track["Album"]["Artist"];

			$this->out(sprintf("\t<info>%d\t%s</info>", $this->LastfmTrack->getData("Track.id"), $this->LastfmTrack->getData("Track.title")));
			$this->LastfmTrack->updateCached();
		}
	}
}

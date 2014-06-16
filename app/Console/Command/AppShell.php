<?php
App::uses('Shell', 'Console');
class AppShell extends Shell {

	public $uses = array("Config");
	public $tasks = array(
		"AlbumSnapshotsSync", "ArtistSnapshotsSync",
		"PopulateAlbumDetails", "PopulateArtistDetails", "PopulateTrackDetails",
		"TrackSnapshotsSync", "UpdatePopularArtists", "UpdateSongChallenge"
	);

	// /Applications/MAMP/bin/php/php5.4.10/bin/php app/Console/cake.php App daily themusictank.nvi
	// php app/Console/cake.php App daily themusictank.com
	public function daily()
	{
		$this->out("DAILY");

		$this->Config->setCronStart("daily");

		$this->UpdateSongChallenge->execute();
        $this->ArtistSnapshotsSync->execute();
        $this->AlbumSnapshotsSync->execute();
        $this->TrackSnapshotsSync->execute();

        $this->Config->setCronEnd("daily");
	}

	public function twohours()
	{
		set_time_limit ((HOUR * 2) - 5); // ok bro, you have 1h55 to finish doing your thing
		$this->out("EVERY TWO HOURS");

		$this->Config->setCronStart("twohours");

		// These tasks fail often due to Last.fm's api.
		// Until a noticeable increase in successful responses
		// query them often to make up for failed attempts
        $this->UpdatePopularArtists->execute();
        $this->PopulateArtistDetails->execute();
        $this->PopulateTrackDetails->execute();
        $this->PopulateAlbumDetails->execute();

        $this->Config->setCronEnd("twohours");
	}
}

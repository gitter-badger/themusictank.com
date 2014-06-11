<?php
App::uses('Shell', 'Console');
class AppShell extends Shell {

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
		$this->UpdateSongChallenge->execute();
        $this->ArtistSnapshotsSync->execute();
        $this->AlbumSnapshotsSync->execute();
        $this->TrackSnapshotsSync->execute();
	}

/*	public function weekly()
	{
		$this->out("WEEKLY");
	}*/

	public function twohours()
	{
		$this->out("EVERY TWO HOURS");

		// These tasks fail often due to Last.fm's api.
		// Until a noticeable increase in successful responses
		// query them often to make up for failed attempts
        $this->UpdatePopularArtists->execute();
        $this->PopulateArtistDetails->execute();
        $this->PopulateTrackDetails->execute();
        $this->PopulateAlbumDetails->execute();
	}
}

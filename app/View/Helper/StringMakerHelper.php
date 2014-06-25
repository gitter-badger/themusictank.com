<?php

class StringMakerHelper extends AppHelper {

    var $helpers = array('Html');

    /**
     * From a daily challenge dataset, build an interesting introductary
     * paragraph. This should look as natural as possible.
     */
	public function composeDailyChallengeIntro($dailyChallenge)
	{
		$randomSeed = $dailyChallenge["Track"]["id"] % 2;
		$strings = array();

		$newTrack = array(
			sprintf("The challenge can be found off of the recently released <strong>%s</strong>, by <strong>%s</strong>.", $dailyChallenge["Album"]["name"], $dailyChallenge["Album"]["Artist"]["name"]),
			sprintf("This one just came out, but is it really any good? This track can found on %, by <strong>%s</strong>.", $dailyChallenge["Album"]["name"], $dailyChallenge["Album"]["Artist"]["name"]),
		);
		$notable = array(
			sprintf("Today's challenge is off a notable release by <strong>%s</strong>. How well does it compare to the other tracks on <strong>%s</strong>?", $dailyChallenge["Album"]["Artist"]["name"], $dailyChallenge["Album"]["name"]),
			sprintf("You may have already heard the album, but that doesn't mean this track is any good. Today's challenge is a song off of <strong>%s</strong> by <strong>%s</strong>?", $dailyChallenge["Album"]["name"], $dailyChallenge["Album"]["Artist"]["name"]),
		);
		$nothingSpecial = array(
			sprintf("The daily track challenge can be found on <strong>%s</strong>. The album was released by <strong>%s</strong> on <strong>%s</strong>.", $dailyChallenge["Album"]["name"], $dailyChallenge["Album"]["Artist"]["name"], date("m-d-Y", $dailyChallenge["Album"]["release_date"])),
			sprintf("Today's challenge is the <strong>%s</strong>th track of <strong>%s</strong> the <strong>%s</strong> album.", $dailyChallenge["Track"]["track_num"], $dailyChallenge["Album"]["name"], $dailyChallenge["Album"]["Artist"]["name"]),
		);
		$longTrack = array(
			"That one will take some dedication. Today's challenge is a long one.",
			"Being longer than most tracks, do you think this one justifies its length?"
		);
		$shortTrack = array(
			"Short and sweet, this is one of the shortest tracks on the album.",
			"Because size isn't everything... right? A short track for today's challenge."
		);

		if((int)$dailyChallenge["Album"]["is_newrelease"] > 0) {
    		$strings[] = $newTrack[$randomSeed];
		}
		elseif((int)$dailyChallenge["Album"]["notability"] <= 2 ) {
    		$strings[] = $notable[$randomSeed];
		}
		else {
    		$strings[] = $nothingSpecial[$randomSeed];
		}

		if((int)$dailyChallenge["Track"]["duration"] > (MINUTE * 6)) {
			$strings[] = $longTrack[$randomSeed];
		}
		elseif((int)$dailyChallenge["Track"]["duration"] < (MINUTE * 2)) {
			$strings[] = $shortTrack[$randomSeed];
		}

		return implode(" ", $strings);
	}

	public function composeTrackAppreciation($snapshot, $track, $album, $artist)
	{
		$randomSeed = $track["id"] % 2;
		$strings = array();

		if((int)$snapshot["neutral_pct"] >= 50)
		{
			if((int)$snapshot["neutral_pct"] > 90)
				$descriptor = "entirely";
			elseif((int)$snapshot["neutral_pct"] > 60)
				$descriptor = "mostly";

			$neutral = array(
				sprintf("The general concensus regarding %s is %s neutral.", $track["title"], $descriptor),
				sprintf("From what we have compiled, reviewers have been %s neutral of %s.", $descriptor, $track["title"])
			);

			$strings[] = $neutral[$randomSeed];
		}

		if((int)$snapshot["disliking_pct"] >= 50)
		{
			if((int)$snapshot["disliking_pct"] > 90)
				$descriptor = "entirely";
			elseif((int)$snapshot["disliking_pct"] > 60)
				$descriptor = "mostly";

			$disliked = array(
				sprintf("The general concensus regarding %s is %s neutral.", $track["title"], $descriptor),
				sprintf("From what we have compiled, reviewers have been %s neutral of %s.", $descriptor, $track["title"])
			);

			$strings[] = $disliked[$randomSeed];
		}

		if((int)$snapshot["liking_pct"] >= 50)
		{
			if((int)$snapshot["liking_pct"] > 90)
				$descriptor = "entirely";
			elseif((int)$snapshot["liking_pct"] > 60)
				$descriptor = "mostly";

			$liked = array(
				sprintf("The general concensus regarding %s is %s neutral.", $track["title"], $descriptor),
				sprintf("From what we have compiled, reviewers have been %s neutral of %s.", $descriptor, $track["title"])
			);

			$strings[] = $liked[$randomSeed];
		}

		return implode(" ", $strings);
	}

	public function composeAlbumAppreciation($snapshot, $album, $artist)
	{
		$randomSeed = $album["id"] % 2;
		$strings = array();

		if((int)$snapshot["neutral_pct"] >= 50)
		{
			if((int)$snapshot["neutral_pct"] > 90)
				$descriptor = "entirely";
			elseif((int)$snapshot["neutral_pct"] > 60)
				$descriptor = "mostly";

			$neutral = array(
				sprintf("The general concensus regarding %s is %s neutral.", $album["name"], $descriptor),
				sprintf("From what we have compiled, reviewers have been %s neutral of %s.", $descriptor, $album["name"])
			);

			$strings[] = $neutral[$randomSeed];
		}

		if((int)$snapshot["disliking_pct"] >= 50)
		{
			if((int)$snapshot["disliking_pct"] > 90)
				$descriptor = "entirely";
			elseif((int)$snapshot["disliking_pct"] > 60)
				$descriptor = "mostly";

			$disliked = array(
				sprintf("The general concensus regarding %s is %s neutral.", $album["name"], $descriptor),
				sprintf("From what we have compiled, reviewers have been %s neutral of %s.", $descriptor, $album["name"])
			);

			$strings[] = $disliked[$randomSeed];
		}

		if((int)$snapshot["liking_pct"] >= 50)
		{
			if((int)$snapshot["liking_pct"] > 90)
				$descriptor = "entirely";
			elseif((int)$snapshot["liking_pct"] > 60)
				$descriptor = "mostly";

			$liked = array(
				sprintf("The general concensus regarding %s is %s neutral.", $album["name"], $descriptor),
				sprintf("From what we have compiled, reviewers have been %s neutral of %s.", $descriptor, $album["name"])
			);

			$strings[] = $liked[$randomSeed];
		}

		return implode(" ", $strings);
	}

	public function composeTimedAppreciation($snapshot, $duration)
	{
		$strings 	= array();
		$pcts 		= array(
			"enjoyment" 	=> $snapshot["liking_pct"],
			"displeasure" 	=> $snapshot["disliking_pct"],
			"meh" 			=> $snapshot["neutral_pct"]
		);
		asort($pcts, SORT_NUMERIC);

		foreach ($pcts as $key => $value) {
			$time = date("i\m s\s", ($value * (int)$duration / 100));
			$strings[] = sprintf("<strong>%s</strong> of <em>%s</em>", $time, $key);
		}

		return implode(" ", $strings);
	}

	public function composeTrackPresentation($lastfmTrack, $track, $album, $artist)
	{
		if(empty($lastfmTrack["wiki"]))
		{
			$albumLink = $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"]));
			$artistLink = $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"]));

			$trackIdxStr = $track["track_num"] . "<sup>th</sup>";
			if($track["track_num"] == 3) {
				$trackIdxStr = "third";
			}
			elseif($track["track_num"] == 2) {
				$trackIdxStr = "second";
			}
			elseif($track["track_num"] == 1) {
				$trackIdxStr = "first";
			}

		    return sprintf(__("This is the %s track off of %s. The album by %s that has been released on %s."), $trackIdxStr, $albumLink, $artistLink, date("F j Y", $album["release_date"]));
		}

	    return $lastfmTrack["wiki"];
	}

	public function composeAlbumPresentation($lastfmAlbum, $album, $artist)
	{
		if(empty($lastfmTrack["wiki"]))
		{
		    return sprintf(__("This is an album by %s."), $artist["name"]);
		}
		else
		{
		    return $lastfmAlbum["wiki"];
		}
	}

	public function composeTrackHighlight($trackReviewSnapshot)
	{
		$startTop 		= date("i\m s\s", $trackReviewSnapshot["top"][0]);
		$endTop 		= date("i\m s\s", $trackReviewSnapshot["top"][1]);
		$startBottom 	= date("i\m s\s", $trackReviewSnapshot["bottom"][0]);
		$endBottom 		= date("i\m s\s", $trackReviewSnapshot["bottom"][1]);
		return sprintf(__("The most popular area of the song ranges from %s to %s."), $startTop, $endTop);
	}

	public function composeTrackLowpoint($trackReviewSnapshot)
	{
		$startBottom 	= date("i\m s\s", $trackReviewSnapshot["bottom"][0]);
		$endBottom 		= date("i\m s\s", $trackReviewSnapshot["bottom"][1]);
		return sprintf(__("The least popular area ranges from %s to %s."), $startBottom, $endBottom);
	}

}

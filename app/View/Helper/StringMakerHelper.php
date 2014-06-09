<?php

class StringMakerHelper extends AppHelper {

    /**
     * From a daily challenge dataset, build an interesting introductary
     * paragraph. This should look as natural as possible.
     */
	public function composeDailyChallengeIntro($dailyChallenge)
	{
		$randomSeed = $dailyChallenge["Track"]["id"] % 2;
		$strings = array();

		$newTrack = array(
			sprintf("The challenge can be found off of the recently released %s, by %s.", $dailyChallenge["Album"]["name"], $dailyChallenge["Album"]["Artist"]["name"]),
			sprintf("This one just came out, but is it really any good? This track can found on %, by %s.", $dailyChallenge["Album"]["name"], $dailyChallenge["Album"]["Artist"]["name"]),
		);
		$notable = array(
			sprintf("Today's challenge is off a notable release by %s. How well does it compare to the other tracks on %s?", $dailyChallenge["Album"]["Artist"]["name"], $dailyChallenge["Album"]["name"]),
			sprintf("You may have already heard the album, but that doesn't mean this track is any good. Today's challenge is a song off of %s by %s?", $dailyChallenge["Album"]["name"], $dailyChallenge["Album"]["Artist"]["name"]),
		);
		$nothingSpecial = array(
			sprintf("The daily track challenge can be found on %s. The album was released by %s on %s.", $dailyChallenge["Album"]["name"], $dailyChallenge["Album"]["Artist"]["name"], date("m-d-Y", $dailyChallenge["Album"]["release_date"])),
			sprintf("Today's challenge is the %th track of %s, by %s.", $dailyChallenge["Track"]["track_num"], $dailyChallenge["Album"]["name"], $dailyChallenge["Album"]["Artist"]["name"]),
		);
		$longTrack = array(
			"That one will take some dedication. Today's challenge is a long one.",
			"Do you think this track justifies its length?"
		);
		$shortTrack = array(
			"Short and sweet, this is one of the shortest tracks on the album.",
			"Because size isn't everything... right? A short track for today's challenge."
		);

		if((int)$dailyChallenge["Album"]["is_newrelease"] > 0) {
    		$strings[] = $newTrack[$randomSeed];
		}
		elseif((int)$dailyChallenge["Album"]["notability"] >= 90 ) {
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
			if((int)$snapshot["neutral_pct"] > 60)
				$descriptor = "mostly";
			elseif((int)$snapshot["neutral_pct"] > 90)
				$descriptor = "entirely";

			$neutral = array(
				sprintf("The general concensus regarding %s is %s neutral.", $track["title"], $descriptor),
				sprintf("From what we have compiled, reviewers have been %s neutral of %s.", $descriptor, $track["title"])
			);

			$strings[] = $neutral[$randomSeed];
		}

		if((int)$snapshot["disliking_pct"] >= 50)
		{
			if((int)$snapshot["disliking_pct"] > 60)
				$descriptor = "mostly";
			elseif((int)$snapshot["disliking_pct"] > 90)
				$descriptor = "entirely";

			$disliked = array(
				sprintf("The general concensus regarding %s is %s neutral.", $track["title"], $descriptor),
				sprintf("From what we have compiled, reviewers have been %s neutral of %s.", $descriptor, $track["title"])
			);

			$strings[] = $disliked[$randomSeed];
		}

		if((int)$snapshot["liking_pct"] >= 50)
		{
			if((int)$snapshot["liking_pct"] > 60)
				$descriptor = "mostly";
			elseif((int)$snapshot["liking_pct"] > 90)
				$descriptor = "entirely";

			$liked = array(
				sprintf("The general concensus regarding %s is %s neutral.", $track["title"], $descriptor),
				sprintf("From what we have compiled, reviewers have been %s neutral of %s.", $descriptor, $track["title"])
			);

			$strings[] = $liked[$randomSeed];
		}

		return implode(" ", $strings);
	}

	public function composeTimedAppreciation($pcts, $track)
	{
		$strings = array();

		foreach ($pcts as $key => $value) {
			$strings[] = sprintf("<strong>%s</strong> seconds of <em>%s</em>", ($value * (int)$track["duration"] / 100), $key);
		}

		return implode(" ", $strings);
	}

}

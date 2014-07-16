<?php
class TrackWavesSyncTask extends Shell {

    public $uses = array('TrackYoutube', 'WavParser');

    public function execute()
    {
        $trackIdsToSync = array();

        $this->out("Crawling <comment>youtube</comment> videos for waveforms");

        try {

            $mp3filepath = "tmp";
            $tmpname = "tmp";
            $tracks = $this->TrackYoutube->getMissingWaveforms();

            $results = array();

            foreach($tracks as $track)
            {
                // https://github.com/afreiday/php-waveform-svg/blob/master/php-waveform-svg.php

                // step 1: video to mp3
                //http://rg3.github.io/youtube-dl/
                //youtube-dl --extract-audio --audio-format mp3 -l [YOUTUBE VIDEO LINK]
                exec("youtube-dl $mp3filepath.mp3 --extract-audio --audio-format mp3 -l https://www.youtube.com/watch?v=" . $track["youtube_key"]);

                // step 2: mp3 to wave
                exec("lame {$mp3file}.mp3 -m m -S -f -b 16 --resample 8 {$tmpname}.mp3 && lame -S --decode {$tmpname}.mp3 {$tmpname}.wav");

                // step 3 : parse wave
                $results[] = array(
                    $track["id"],
                    $this->WavParser->parse(10, "{$tmpname}.wav")
                );
            }
        }
        catch(Exception $e) {}

        @unlink("{$mp3file}.mp3");
        @unlink("{$tmpname}.wav");

        if(count($results) > 0) {
            $this->TrackYoutube->saveAll($results);
        }

        $this->out("\t<info>Completed</info>");
    }
}

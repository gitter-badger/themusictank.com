<?php
class TrackWavesSyncTask extends Shell {

    public $uses = array('TrackYoutube', 'WavParser');

    public function execute()
    {
        $trackIdsToSync = array();

        $this->out("Crawling <comment>youtube</comment> videos for waveforms");

        try {
            $tracks = $this->TrackYoutube->getMissingWaveforms();
            $results = array();

            $youtubedl = ROOT . DS . APP_DIR . DS . "Lib" . DS .  "youtube-dl";
            $workingFilePath = ROOT . DS . APP_DIR . DS . "tmp" . DS .  "cache" . DS . "youtubedl" . DS;

            foreach($tracks as $track)
            {
                // Fetch the youtube video

                $runYoutubeDownload = "$youtubedl -o ".$workingFilePath."file.mp4 https://www.youtube.com/watch?v=" . $track["TrackYoutube"]["youtube_key"];
                $runVLCEncode = Configure::read('VLC') . ' -I dummy -v '.$workingFilePath.'file.mp4 --sout="#transcode{vcodec=none,acodec=s16l,ab=64,channels=1,samplerate=8000,scodec=none,soverlay}:standard{mux=wav,access=file{no-overwrite},dst='.$workingFilePath.'file.wav}" vlc://quit';

                $this->out($runYoutubeDownload);
                exec($runYoutubeDownload);

                if(file_exists($workingFilePath."file.mp4"))
                {
                    // Convert video file to low quality wav
                    $this->out($runVLCEncode);
                    exec($runVLCEncode);

                    if(file_exists($workingFilePath."file.wav"))
                    {
                        // Analyze the wav to get the wavform.
                        $results[] = array(
                            "id"        => $track["TrackYoutube"]["id"],
                            "waveform"  => $this->WavParser->parse($workingFilePath."file.wav")
                        );
                    }

                }
                else {
                    $this->log($workingFilePath."file.mp4 could not be created");
                }

                @unlink($workingFilePath."file.mp4");
                @unlink($workingFilePath."file.wav");
            }
        }
        catch(Exception $e) {
            $this->log($e->getMessage());
        }

        if(count($results) > 0) {
            $this->TrackYoutube->saveAll($results);
        }

        $this->out("\t<info>Completed</info>");
    }
}

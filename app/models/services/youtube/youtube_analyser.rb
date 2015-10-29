module Services
    module Youtube
        class YoutubeAnalyser < Services::Base
            require 'wavefile'
            require 'tmpdir'

            def self.run
                get_outdated_tracks.limit(2).each do |track|

                    yt_to_wav_data track

                    File.delete(vid_file_path)
                    File.delete(wav_file_path)

                end if can_fetch
            end

            private

                def self.get_outdated_tracks
                   Track.find_with_no_soundwave
                     .where(:youtube_key != nil)
                     .where("youtube_key <> ''")
                end

                def self.ytdl_bin
                    Dir.getwd + '/bin/youtube-dl'
                end

                def self.vlc_bin
                    '/Applications/VLC.app/Contents/MacOS/VLC'
                end

                def self.can_fetch
                    File.exist?(ytdl_bin)
                end

                def self.vid_file_path
                    Dir.tmpdir + "/file.mp4"
                end

                def self.wav_file_path
                    Dir.tmpdir + "/file.wav"
                end

                def self.fetch track
                    cmd = sprintf("python %s -o %s https://www.youtube.com/watch?v=%s", ytdl_bin, vid_file_path, track.youtube_key)
                    system(cmd)
                end

                def self.make_waveform
                    cmd = sprintf("%s -I dummy -v %s --sout=\"#transcode{vcodec=none,acodec=s16l,ab=64,channels=1,samplerate=8000,scodec=none,soverlay}:standard{mux=wav,access=file{no-overwrite},dst=%s}\" vlc://quit",
                        vlc_bin, vid_file_path, wav_file_path)
                    system cmd
                end

                def self.parse_waveform
                    WaveFile::Reader.new(wav_file_path).each_buffer(4096) do |buffer|
                      puts "Buffer number of channels:   #{buffer.channels}"
                      puts "Buffer bits per sample:      #{buffer.bits_per_sample}"
                      puts "Number of samples in buffer: #{buffer.samples.length}"
                      puts "First 10 samples in buffer:  #{buffer.samples[0...10].inspect}"
                      puts "--------------------------------------------------------------"
                    end
                end

                def self.yt_to_wav_data track
                    fetch track
                    make_waveform
                    parse_waveform
                end

        end
    end
end

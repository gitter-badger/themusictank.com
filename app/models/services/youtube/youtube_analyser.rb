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
                    puts cmd
                    system cmd
                end

                def self.make_waveform
                    cmd = sprintf("%s -I dummy -v %s --sout=\"#transcode{vcodec=none,acodec=s16l,ab=64,channels=1,samplerate=8000,scodec=none,soverlay}:standard{mux=wav,access=file{no-overwrite},dst=%s}\" vlc://quit",
                        vlc_bin, vid_file_path, wav_file_path)
                    puts cmd
                    system cmd
                end

                def self.parse_waveform
                    samples = []
                    
                    max = 32768 # on 16 bit
                    # min = -32768
                    WaveFile::Reader.new(wav_file_path).each_buffer(4096) do |buffer|
                        buffer.samples.each do |sample|
                            samples << (sample + max).to_f / (max * 2).to_f
                        end
                    end

                    # save paged information: 5 per second
                    # based on this doc : http://www.audiomountain.com/tech/audio-file-size.html
                    # 705.6 per second, we round and keep only 2 per second: 352.0
                    kbps = 352.0
                    roundedSamples = []
                    total = 0
                    
                    samples.each_with_index {|sample, index|
                        total += sample
                        if index % kbps === 0.0
                            roundedSamples << (total / kbps).round(2)
                            total = 0.0
                        end
                    }

                    roundedSamples                    
                end

                def self.yt_to_wav_data track
                    fetch track
                    make_waveform
                    track.track_soundwave = TrackSoundwave.new
                    track.track_soundwave.soundwave = parse_waveform
                    track.track_soundwave.save!
                end

        end
    end
end

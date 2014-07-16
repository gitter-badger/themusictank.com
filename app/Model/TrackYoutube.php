<?php
class TrackYoutube extends OEmbedable
{
    public $belongsTo   = array('Track');
    public $actsAs = array('Youtube');

    public function beforeSave($options = array())
    {
        if(array_key_exists("waveform", $this->data[$this->alias]) && !is_string($this->data[$this->alias]["waveform"]))
        {
            $this->data[$this->alias]["waveform"] = json_encode($this->data[$this->alias]["waveform"]);
        }

        return true;
    }

    public function afterFind($results, $primary = false)
    {
        foreach($results as $idx => $row)
        {
            if(array_key_exists($this->alias, $row))
            {
                if(array_key_exists("waveform", $row[$this->alias]) && is_string($row[$this->alias]["waveform"]))
                {
                    $results[$idx][$this->alias]["waveform"] = json_decode($row[$this->alias]["waveform"]);
                }
            }
        }
        return $results;
    }

    public function searchApi($artist)
    {
        $artistName = Hash::get($artist, "Artist.name");
        $trackName = $this->getData("Track.title");

        $videoId = $this->getRemoteVideoId($artistName . "-" . $trackName);

        $result = array(
            "track_id"      => $this->getData("Track.id"),
            "youtube_key"   => $videoId,
            "waveform"      => null // since this is a new video, ensure the waveform is not set
        );

        if(Hash::check($this->data, "TrackYoutube.id"))
        {
             $result["id"] = $this->getData("TrackYoutube.id");
        }

        $this->save($result);
        return $videoId;
    }

    public function getMissingWaveforms()
    {
        return $this->TrackYoutube->find("all", array(
            "conditions" => array(
                array("not" => array ( "TrackYoutube.youtube_key" => null),
                array("TrackYoutube.youtube_key" => null)
            )
        ));
    }

}

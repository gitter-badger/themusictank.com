<?php
    $config = array(            
        "containerSelector"     => ".play-" . $track["slug"],
        "domain"    => $_SERVER['SERVER_NAME'],
        "type"      => $preferredPlayer,
        "visual"    => "equilizer",
        "trackDuration" => (int)$track["duration"],
        "debug"     => (Configure::read('debug') < 1) ? "false" : "true"
    );    

    if($preferredPlayer == "rdio")
    {
        $config["swfRoot"] = "http://www.rdio.com/api/swf/";
        $config["swfId"] = "play-" . $track["slug"];        
        $config["playbackToken"]  = CakeSession::read("Player.RdioPlaybackToken");
        $config["trackKey"] = $rdioTrack["key"];    
        echo '<b id="play-'.$track["slug"] .'"></b>';
    }
        
    $snapshot["containerSelector"] = ".play-" . $track["slug"] . " canvas";
    $snapshot["curve_snapshot"] = json_decode($snapshot["curve_snapshot"]);;
    $snapshot["trackDuration"] = (int)$track["duration"];
?>
<section class="player play-<?php echo $track["slug"]; ?>">
    <canvas></canvas>        
    <div class="cursor"></div>
    <div class="seek">            
        <div class="bar"><div class="progress"><span class="knob"></span></div></div>
        <span class="time"></span>
    </div>
    <div class="controls">
        <ul style="float:right;">
            <li><?php echo $this->Html->link(__("Review"), array('controller' => 'player', 'action' => 'play', $track["slug"])); ?></li>
        </ul>
        <ul>
            <li><button type="button" title="<?php echo __("Play"); ?>" name="play"><?php echo __("Play"); ?></button></li>
        </ul>
    </div>     
    <script>
        (function(){
            var p = new tmt.player(<?php echo json_encode($config); ?>);
            p.setupCallback();
            p.init();
            var g = new tmt.graph(<?php echo json_encode($snapshot); ?>);
            g.init();
        })();
    </script>
</section>
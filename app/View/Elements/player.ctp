<?php
    $config = array(            
        "containerSelector"     => ".play-" . $track["slug"],
        "domain"    => $_SERVER['SERVER_NAME'],
        "type"      => $preferredPlayer,
        "visual"    => "equilizer",
        "trackDuration" => (int)$track["duration"],
        "debug"     => (Configure::read('debug') < 1) ? "false" : "true"
    );    

    $graphConfig = array(
        "containerSelector" => ".play-" . $track["slug"] . " canvas",
        "trackDuration" => (int)$track["duration"],
        "curve_snapshot" => $trackReviewSnapshot["curve_snapshot"]
    );
    
    $isLogged = $this->Session->read("Auth.User.User.id");
    
    if($preferredPlayer == "rdio")
    {
        $config["swfRoot"] = "http://www.rdio.com/api/swf/";
        $config["swfId"] = "play-" . $track["slug"];        
        $config["playbackToken"]  = CakeSession::read("Player.RdioPlaybackToken");
        $config["trackKey"] = $rdioTrack["key"];    
        echo '<b id="play-'.$track["slug"] .'"></b>';
    }
    else {         
        $config["trackTitle"] = $track["title"];
    }    
    
    if(isset($friendsReviewSnapshot))
    {
        $graphConfig["friends_curve_snapshot"] = $friendsReviewSnapshot["curve_snapshot"]["curve"];
    }
    
?>
<section class="player chart timed <?php echo $preferredPlayer; ?> <?php echo $isLogged ? 'logged' : 'not-logged' ?> play-<?php echo $track["slug"]; ?>">
    <canvas></canvas>        
    <div class="cursor"></div>    
    
    <ul>
        <li><span style="background:#999;">&nbsp;&nbsp;&nbsp;</span><?php echo __("Everyone"); ?></li>
        <li><span style="background:#4285f4;">&nbsp;&nbsp;&nbsp;</span><?php echo __("People you are subscribed to"); ?></li>
    </ul>
    
    <div class="seek">            
        <div class="bar"><div class="progress"><span class="knob"></span></div></div>
    </div>
    <div class="controls">        
        <ul style="float:right;">
            <li><?php echo $this->Html->link(__("Review"), array('controller' => 'player', 'action' => 'play', $track["slug"])); ?></li>
        </ul>
        <ul>
            <li><button type="button" title="<?php echo __("Play"); ?>" name="play"><?php echo __("Play"); ?></button></li>
            <li class="time">--:-- / --:--</li>
        </ul>
        
        <?php  if($preferredPlayer == "mp3") : ?>
            <div class="drop">            
                <p><?php echo sprintf(__("Drop your mp3 of '%s' here"), $track["title"]); ?></p>
                <input type="file" name="file" />
            </div>       
            <?php if(!$isLogged) : ?>
                <div class="or-login">
                    <p><?php echo $this->Html->link(__("or login"), array('controller' => 'users', 'action' => 'login', "?" => array("rurl" => "/tracks/view/".$track["slug"]))); ?></p>
                </div>
            <?php endif; ?>
            <div class="parsing-mp3">
                <p><?php echo __("Checking the ID3 tag on the file"); ?>...</p>
                <p class="error tag">
                    <?php echo __("The file failed our ID3 tag validation. We get pretty serious with those and we need a mp3 that looks similar and also has it's ID3 information properly filled."); ?>
                    <button name="try-again"><?php echo __("Try again"); ?></button>
                </p>
                <p class="error length">
                    <?php echo __("The file doesn't have the same length as we were expecting. We can't match it with our version."); ?>
                    <button name="try-again"><?php echo __("Try again"); ?></button>
                </p>
            </div>        
        <?php endif; ?>        
    </div>     
    <script>
        (function(){
            new tmt.player(<?php echo json_encode($config); ?>).setupCallback().init();
            new tmt.graph(<?php echo json_encode($graphConfig); ?>).init();
        })();
    </script>
</section>
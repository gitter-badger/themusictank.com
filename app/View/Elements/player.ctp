<?php
    $config = array(            
        "containerSelector"     => ".play-" . $track["slug"],
        "domain"    => $_SERVER['SERVER_NAME'],
        "type"      => $preferredPlayer,
        "trackDuration" => (int)$track["duration"],
        "visual"    => "equilizer",
        "debug"     => (Configure::read('debug') < 1) ? "false" : "true"
    );    
    $playerClassName = "Mp3";
    $isLogged = $this->Session->read("Auth.User.User.id");        
    
    $graphConfig = array(
        "containerSelector" => ".play-" . $track["slug"] . " canvas",
        "trackDuration" => (int)$track["duration"],
        "curve_snapshot" => $trackReviewSnapshot["curve_snapshot"],
        "range_snapshot" => $trackReviewSnapshot["range_snapshot"],
        "subs_curve_snapshot" => isset($subsTrackReviewSnapshot) && count($subsTrackReviewSnapshot) ? $subsTrackReviewSnapshot["curve_snapshot"] : null, 
        "subs_range_snapshot" => isset($subsTrackReviewSnapshot) && count($subsTrackReviewSnapshot) ? $subsTrackReviewSnapshot["range_snapshot"] : null,
        "user_curve_snapshot" => isset($userAlbumReviewSnapshot) && count($userAlbumReviewSnapshot) ? $userAlbumReviewSnapshot["curve_snapshot"] : null, 
        "user_range_snapshot" => isset($userAlbumReviewSnapshot) && count($userAlbumReviewSnapshot) ? $userAlbumReviewSnapshot["range_snapshot"] : null
    );
        
    
    if($preferredPlayer == "rdio")
    {
        $playerClassName = "Rdio";
        $config["swfRoot"] = "http://www.rdio.com/api/swf/";
        $config["swfId"] = "play-" . $track["slug"];        
        $config["playbackToken"]  = CakeSession::read("Player.RdioPlaybackToken");
        $config["trackKey"] = $rdioTrack["key"];    
        echo '<b id="play-'.$track["slug"] .'"></b>';
    }
    else {         
        $config["trackTitle"] = $track["title"];
    }    
?>
<section class="player chart timed <?php echo $preferredPlayer; ?> <?php echo $isLogged ? 'logged' : 'not-logged' ?> play-<?php echo $track["slug"]; ?>">
    
    <canvas></canvas>
    
    <div class="cursor"></div>  
    
    <ul class="legend">
        <li class="everyone">
            <label>
                <input type="checkbox" name="view" value="everyone" checked="checked" />
                <?php echo __("Everyone"); ?>
            </label>
        </li>
        <?php if($isLogged) : ?>
        <li class="friends">
            <label>
                <input type="checkbox" name="view" value="subs" checked="checked" />
                <?php echo __("People you are subscribed to"); ?>
            </label>
        </li>
        <li class="you">
            <label>
                <input type="checkbox" name="view" value="user" checked="checked" />
                <?php echo __("You"); ?>
            </label>
        </li>
        <?php endif; ?>
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
<script>$(function(){
new tmt.<?php echo $playerClassName; ?>(<?php echo json_encode($config); ?>).run();
new tmt.Graph(<?php echo json_encode($graphConfig); ?>);
});</script>
</section>
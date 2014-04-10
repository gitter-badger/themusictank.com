<?php
    $config = array(            
        "containerSelector"     => ".play-" . $track["slug"],
        "domain"    => $_SERVER['SERVER_NAME'],
        "type"      => $preferredPlayer,
        "trackDuration" => (int)$track["duration"],
        "visual"    => "equilizer",
        "saveEquilizer"    => count($track["wavelength"]),
        "equilizeUrl" => Router::url(array("controller" => "ajax", "action" => "savewave", $track["slug"], sha1($track["slug"] . $track["id"] . "foraiurtheplayer"))),
        "debug"     => Configure::read('debug') > 0
    );    
    $playerClassName = "Mp3";
    $isLogged = $this->Session->read("Auth.User.User.id");        
    
    $graphConfig = array(
        "containerSelector" => ".play-" . $track["slug"] . " canvas",
        "trackDuration" => (int)$track["duration"],
        "equilizerData"    => $track["wavelength"]
    );
    
    if(isset($trackReviewSnapshot) && count($trackReviewSnapshot))
    {
        $graphConfig["curves"][ "tankers" ] = array(
            "label" => __("Everyone"),
            "data" => $trackReviewSnapshot["curve_snapshot"],
            "color" => "#999999"
        );         
        $graphConfig["ranges"][ "tankers" ] = array(
            "label" => __("Everyone"),
            "data" => $trackReviewSnapshot["range_snapshot"],
            "color" => "rgba(66, 66, 66,.4)"
        ); 
    }     
    
    if(isset($subsTrackReviewSnapshot) && count($subsTrackReviewSnapshot))
    {
        $graphConfig["curves"][ "subs" ] = array(
            "label" => __("Your subscriptions"),
            "data" => $subsTrackReviewSnapshot["curve_snapshot"],
            "color" => "#4285f4"
        );         
        $graphConfig["ranges"][ "subs" ] = array(
            "label" => __("Your subscriptions"),
            "data" => $subsTrackReviewSnapshot["range_snapshot"],
            "color" => "rgba(66, 133, 244, .4)"
        ); 
    }
    
    if(isset($userTrackReviewSnapshot) && count($userTrackReviewSnapshot))
    {
        $graphConfig["curves"][ "you" ] = array(
            "label" => __("You"),
            "data" => $userTrackReviewSnapshot["curve_snapshot"],
            "color" => "rgb(90, 20, 244)"
        );         
        $graphConfig["ranges"][ "you" ] = array(
            "label" => __("You"),
            "data" => $userTrackReviewSnapshot["range_snapshot"],
            "color" => "rgba(90, 20, 244, .4)"            
        ); 
    }
    
    if(isset($viewingTrackReviewSnapshot) && count($viewingTrackReviewSnapshot))
    {
        $graphConfig["curves"][ "user" ] = array(
            "label" => $viewingUser["firstname"] . " " . $viewingUser["lastname"],
            "data" => $viewingTrackReviewSnapshot["curve_snapshot"],
            "color" => "rgb(90, 20, 244)"
        );         
        $graphConfig["ranges"][ "viewingUser" ] = array(
            "label" => $viewingUser["firstname"] . " " . $viewingUser["lastname"],
            "data" => $viewingTrackReviewSnapshot["range_snapshot"],
            "color" => "rgba(90, 20, 244, .4)"            
        ); 
    }
    
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
    <?php if(count($graphConfig["curves"]) > 0) : ?>    
    <ul class="legend">      
        <?php foreach($graphConfig["curves"] as $key => $curveInfo) : ?>
        <li class="<?php echo $key; ?>">
            <label>
                <input type="checkbox" name="view" value="<?php echo $key; ?>" checked="checked" />
                <?php echo $curveInfo["label"]; ?>
            </label>
        </li>
        <?php endforeach; ?>
    </ul> 
    <?php endif; ?>
        
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
        <?php if($preferredPlayer == "mp3") : ?>
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
        <?php elseif($preferredPlayer == "rdio") : ?>
            <div class="flash-required">
                <p><?php echo __("Flash support is required to use the Rdio player."); ?></p>
            </div>
        <?php endif; ?>        
    </div>     
<script>$(function(){
new tmt.<?php echo $playerClassName; ?>(<?php echo json_encode($config); ?>).run();
new tmt.Graph(<?php echo json_encode($graphConfig); ?>);
});</script>
</section>
<?php
    $tmtTrackKey = implode("-", array($artist["id"], $album["id"], $track["id"], $this->Session->read('Auth.User.User.id'), uniqid()));
    $config = array(            
        "containerSelector"     => ".play-" . $track["slug"],
        "domain"    => $_SERVER['SERVER_NAME'],
        "type"      => $preferredPlayer,
        "trackDuration" => (int)$track["duration"],
        "visual"    => "frequency",
        "debug"     => (Configure::read('debug') < 1) ? "false" : "true",
        "tmtUrl" => Router::url(array("controller" => "review_frames", "action" => "save", $tmtTrackKey))                
    );    
    
    $isLogged = $this->Session->read("Auth.User.User.id");        
        
    if($preferredPlayer == "rdio")
    {
        $config["swfRoot"] = "http://www.rdio.com/api/swf/";
        $config["swfId"] = "play-" . $track["slug"];        
        $config["playbackToken"]  = CakeSession::read("Player.RdioPlaybackToken");
        $config["trackKey"] = $rdioTrack["key"];
        $config["startOnReady"] = true;    
        
        echo '<b id="play-'.$track["slug"] .'"></b>';
    }
    else {         
        $config["trackTitle"] = $track["title"];
        $config["startOnReady"] = false;    
    }    
?>
<section class="player review timed <?php echo $preferredPlayer; ?> <?php echo $isLogged ? 'logged' : 'not-logged' ?> play-<?php echo $track["slug"]; ?>">
    
    <div class="ui">
        <canvas></canvas>
        <div class="joystick"><b></b></div> 
    </div>
            
    <div class="seek">            
        <div class="bar"><div class="progress"><span class="knob"></span></div></div>
    </div>
        
    <div class="controls">
        <ul>
            <li><button type="button" title="<?php echo __("Play"); ?>" name="play"><?php echo __("Play"); ?></button></li>
            <li class="time">--:-- / --:--</li>
        </ul>        
        <?php  if($preferredPlayer == "mp3") : ?>
            <div class="drop">            
                <p><?php echo sprintf(__("Drop your mp3 of '%s' here"), $track["title"]); ?></p>
                <input type="file" name="file" />
            </div>
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

    <?php /* this should be in the canvas element
        <div id="ui">        
            <!-- Statuses -->
            <div class="animated statuses label_starpowering">
                <strong>x 4</strong> <?php echo __('Starpowering'); ?>!
            </div>                
            <div class="animated statuses label_suckpowering">
                <strong>x -4</strong> <?php echo __('Suckpowering'); ?>!
            </div>

            <!-- Multiplier -->
            
            <div class="animated multiplier label_multiplier_-3">
                <strong>x -3</strong> <?php echo __("multiplier"); ?>
            </div>
            <div class="animated multiplier label_multiplier_-2">
                <strong>x -2</strong> <?php echo __("multiplier"); ?>
            </div>
            <div class="animated multiplier label_multiplier_-1">
                <strong>x -1</strong> <?php echo __("multiplier"); ?>
            </div>
            <div class="animated multiplier label_multiplier_1">
                <strong>x 1</strong> <?php echo __("multiplier"); ?>
            </div>
            <div class="animated multiplier label_multiplier_2">
                <strong>x 2</strong> <?php echo __("multiplier"); ?>
            </div>
            <div class="animated multiplier label_multiplier_3">
                <strong>x 3</strong> <?php echo __("multiplier"); ?>
            </div>      

            <div class="multiplier-progress">
                <div id="slice">
                    <div class="pie"></div>
                    <div class="pie fill"></div>                        
                </div>
            </div>
            
        </div>
       */ ?>

    <div class="focus-lost">
        <h3><?php echo __("Must retain focus"); ?></h3>
        <p><?php echo __('This window needs to always stay active and in focus for the reviewing process to work, sorry :s'); ?></p>
        <button name="resume"><?php echo __('Resume') ?></button>
        <?php echo $this->Html->link(__("Back to artist page"), array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?>
    </div>       

    <div class="mask loading-mask">
        <div class="icon">Loading</div>
    </div>
    
<script>$(function(){
    var r = new (tmt.Rdio.extend(tmt.Reviewer))(<?php echo json_encode($config); ?>);  
    window.onblur = function(){r.onWindowVisibility(false);};
    window.onfocus = function(){r.onWindowVisibility(true);};
    r.run();
});</script>
</section>
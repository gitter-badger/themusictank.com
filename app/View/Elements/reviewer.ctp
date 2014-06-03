<?php
    $tmtTrackKey = implode("-", array($artist["id"], $album["id"], $track["id"], $this->Session->read('Auth.User.User.id'), uniqid()));
    $shaCheck = sha1($this->Session->read('Auth.User.User.id') . $track["id"] . "user is reviewing something kewl");
    $waveShaCheck = sha1($track["slug"] . $track["id"] . "foraiurtheplayer");
    $config = array(            
        "containerSelector"     => ".play-" . $track["slug"],
        "domain"    => $_SERVER['SERVER_NAME'],
        "type"      => $preferredPlayer,
        "trackDuration" => (int)$track["duration"],
        "visual"    => "frequency",
        "debug"     => Configure::read('debug') > 0,
        "equilizeUrl" => Router::url(array("controller" => "ajax", "action" => "savewave", $track["slug"], $waveShaCheck)),
        "tmtUrl"    => Router::url(array("controller" => "ajax", "action" => "pushrf", $tmtTrackKey, $shaCheck))                
    );    
    
    $isLogged = $this->Session->read("Auth.User.User.id");        
        
    if($preferredPlayer == "rdio")
    {
        $config["swfRoot"] = "http://www.rdio.com/api/swf/";
        $config["swfId"] = "play-" . $track["slug"];        
        $config["playbackToken"]  = $this->Session->read("Player.RdioPlaybackToken");
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
            <li><button type="button" title="<?php echo __("Play"); ?>" name="play" autofocus="autofocus"><?php echo __("Play"); ?></button></li>
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
        <?php elseif($preferredPlayer == "rdio") : ?>
            <div class="flash-required">
                <p><?php echo __("Flash support is required to use the Rdio player."); ?></p>
            </div>
        <?php endif; ?>        
    </div>

    <div class="focus-lost">
        <h3><?php echo __("Must retain focus"); ?></h3>
        <p><?php echo __('This window needs to always stay active and in focus for the reviewing process to work, sorry :s'); ?></p>
        <button name="resume"><?php echo __('Resume') ?></button>
        <?php echo $this->Html->link(__("Back to artist page"), array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?>
    </div>       

    <div class="mask loading-mask">
        <div class="icon">Loading</div>
    </div>
        
    <div class="review-complete">
        <h3><?php echo __("Review completed!"); ?></h3>
        <p><?php echo sprintf(__('You have completed the review of %s'), $track['slug']); ?></p>
        <ul>
            <li>
                <?php echo $this->Html->link(__("View your review's groove"), array('controller' => 'tracks', 'action' => 'by_user', $track["slug"], $this->Session->read('Auth.User.User.slug'))); ?>
            </li>
            <li>
                <?php echo __('Share with your friends'); ?>
                <?php $currentPage = "http://" . $_SERVER['SERVER_NAME'] . Router::url(array('controller' => 'tracks', 'action' => 'by_user', $track["slug"], $this->Session->read('Auth.User.User.slug'))); ?>
                 <a href="https://twitter.com/share" class="twitter-share-button" 
                    data-url="<?php echo $currentPage; ?>" 
                    data-text="<?php echo sprintf(__("View my review of '%s' on @themusictank : "), $track["title"]); ?>"
                    data-lang="en">Tweet</a>
                 <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>        

                 <div class="fb-share-button" data-href="<?php echo $currentPage; ?>" data-type="button_count"></div>
                 <div id="fb-root"></div>
                 <script>(function(d, s, id) {
                   var js, fjs = d.getElementsByTagName(s)[0];
                   if (d.getElementById(id)) return;
                   js = d.createElement(s); js.id = id;
                   js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=497725690321176";
                   fjs.parentNode.insertBefore(js, fjs);
                 }(document, 'script', 'facebook-jssdk'));</script>
            </li>
            <?php if(isset($nextTrack)) : ?>
            <li>
                <?php echo $this->Html->link(sprintf(__("Review next track: %s"), $nextTrack["title"]), array('controller' => 'player', 'action' => 'play', $nextTrack["slug"])); ?>
            </li>
            <?php endif; ?>
        </ul>
    </div>  
    
    
<script>$(function(){
    <?php if($preferredPlayer == "rdio") : ?>
        var r = new tmt.Rdio(<?php echo json_encode($config); ?>);  
    <?php else : ?>
        var r = new tmt.Mp3(<?php echo json_encode($config); ?>);  
    <?php endif; ?>
    console.log(r);
    window.onblur = function(){r.onWindowVisibility(false);};
    window.onfocus = function(){r.onWindowVisibility(true);};
    r.run();
});</script>
</section>
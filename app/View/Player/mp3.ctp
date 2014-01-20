<?php echo $this->Html->script( array('player/mp3', 'lib/id3/id3-minimized')); ?>
  
<div id="drop">
    <div class="aid">
        <h3><?php echo __("Drop your mp3 of"); ?> <?php echo $track["title"]; ?> <?php echo __("here"); ?></h3>
        <input id="fileInput" type="file" name="file" />
    </div>
</div>

<div id="parsingmp3">
    <div class="aid">
        <h3><?php echo __("Checking the ID3 tag on the file"); ?>...</h3>
        <h3 class="error"><?php echo __("The file failed our ID3 tag validation. We get pretty serious with those and we need a mp3 that looks similar and also has it's ID3 information properly filled."); ?></h3>
        <h3 class="lengtherror"><?php echo __("The file doesn't have the same length as we were expecting. We can't match it with our version."); ?></h3>
    </div>
</div>

<div class="feature-unavailable"><?php echo __('Your browser does not support key APIs'); ?></div>

<script>
    $(function(){
        try {
            var p = new tmt.player({
                swfRoot : null,
                swfId : 'apiswf',
                domain : "<?php echo $_SERVER['SERVER_NAME']; ?>",
                playbackToken: null,
                type : "mp3",
                trackKey : null,
                trackTitle : "<?php echo $track["title"]; ?>",
                trackDuration : <?php echo (int)$track["duration"]; ?>,
                albumName : "<?php echo $album["name"]; ?>",
                <?php if(!is_null($album["image"])) : ?>
                albumIcon : '<?php echo $this->Html->image($album["image"], array("alt" => $album["name"], "class" => "thumbnail")); ?>',
                <?php endif; ?>
                artistName : "<?php echo $artist["name"]; ?>",                
                startOnReady : false,
                fakeFrequency : true,
                tmtUrl : "<?php echo Router::url(array("controller" => "review_frames", "action" => "save", implode("-", array($artist["id"], $album["id"], $track["id"], $this->Session->read('Auth.User.User.id'), uniqid() )))); ?>",
                debug : <?php echo (Configure::read('debug') < 1) ? "false" : "true"; ?>
            });
            p.setupCallback();
            p.init();            
            window.onblur = function(){p.onWindowVisibility(false);};
            window.onfocus = function(){p.onWindowVisibility(true);};
            
        } catch(e) { console.log(e.message); }
    });
</script>
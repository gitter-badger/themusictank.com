<?php echo $this->Html->script( array('lib/swf/swfobject', 'player/rdio') ); ?>

<div id="cantstream">
    <div class="aid">
        <h3><?php echo __("Rdio won't allow us to stream the complete song"); ?>...</h3>
    </div>
</div>

<script>
    $(function(){
        try {
            var p = new tmt.player({
                swfRoot : "http://www.rdio.com/api/swf/",
                swfId : 'apiswf',
                domain : "<?php echo $_SERVER['SERVER_NAME']; ?>",
                playbackToken: "<?php echo $playbackToken; ?>",
                type : "rdio",
                trackKey : "<?php echo $rdioTrack["key"]; ?>",
                trackDuration : <?php echo (int)$track["duration"]; ?>,
                startOnReady : true,
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
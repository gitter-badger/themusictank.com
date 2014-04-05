<?php echo $this->Html->script( 'player/rdio' ); ?>
<script>
    $(function(){
        try {
            var p = new tmt.player({  
                type : "spotify"
            });
            p.setupCallback();
            p.init();
        } catch(e) { console.log(e.message); }
    });
</script>



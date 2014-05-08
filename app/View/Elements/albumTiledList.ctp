 <div class="row albums">
    <?php foreach($albums as $album) : 
        $albumData = null;
        $artistData = null;

        if(Hash::check($album, "Albums"))
        {
            $albumData = $album["Albums"];     
        }
        elseif(Hash::check($album, "Album"))
        {
            $albumData = $album["Album"];     
        }
        else {
         $albumData = $album;
        }

        if(Hash::check($album, "Artist"))
        {               
            $artistData = $album["Artist"];
        }
    ?>
        <div class="col-xs-12 col-md-3">
            <div class="thumbnail">
                <?php  echo $this->Html->link(
                        $this->Html->image($this->App->getImageUrl($albumData), array("alt" => $albumData["name"], "title" => $albumData["name"])),
                        array('controller' => 'albums', 'action' => 'view', $albumData["slug"]),
                        array('escape' => false)
                ); ?>
            </div>
            <time datetime="<?php echo date("c", $albumData["release_date"]); ?>"><?php echo date("F j Y", $albumData["release_date"]); ?></time>                
            <h3><?php echo $this->Html->link($albumData["name"], array('controller' => 'albums', 'action' => 'view', $albumData["slug"])); ?></h3>
            <?php if(!is_null($artistData)) : ?>
            <p><?php echo __("By"); ?> <?php echo $this->Html->link($artistData["name"], array('controller' => 'artists', 'action' => 'view', $artistData["slug"])); ?></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
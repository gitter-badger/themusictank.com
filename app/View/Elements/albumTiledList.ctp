 <div class="row tiles albums">
    <?php foreach($albums as $album) :
        $albumData = null;
        $artistData = null;
        $albumScoreData = null;

        if(Hash::check($album, "Albums"))
        {
            $albumData = $album["Albums"];
        }
        elseif(Hash::check($album, "Album"))
        {
            $albumData = $album["Album"];
        }
        else
        {
        	$albumData = $album;
        }

        if(Hash::check($album, "Artist"))
        {
            $artistData = $album["Artist"];
        }

        if(Hash::check($album, "AlbumReviewSnapshot"))
        {
            $albumScoreData = $album["AlbumReviewSnapshot"];
        }

    ?>
        <div class="col-xs-6 col-md-3 album">
            <?php  echo $this->Html->link(
                    $this->Html->image($this->App->getImageUrl($albumData), array("alt" => $albumData["name"], "title" => $albumData["name"])),
                    array('controller' => 'albums', 'action' => 'view', $albumData["slug"]),
                    array('escape' => false, "class" => "thumbnail")
            ); ?>
            <h3><?php echo $this->Html->link($albumData["name"], array('controller' => 'albums', 'action' => 'view', $albumData["slug"])); ?></h3>
            <?php if(!is_null($artistData)) : ?>
            <p><?php echo __("By"); ?> <?php echo $this->Html->link($artistData["name"], array('controller' => 'artists', 'action' => 'view', $artistData["slug"])); ?></p>
            <?php endif; ?>
            <?php if((int)$albumData["release_date"] > 0) : ?>
            <time datetime="<?php echo date("c", $albumData["release_date"]); ?>"><?php echo date("F j Y", $albumData["release_date"]); ?></time>
            <?php endif; ?>
			<?php if(!is_null($albumScoreData) && !is_null($albumScoreData["score"])) :  ?>
                <strong><?php echo $albumScoreData["score"]; ?>%</strong>
        	<?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

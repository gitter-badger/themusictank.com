<ul class="tiled-list artists">
<?php foreach($artists as $artist) : ?>
    <li>           
        <div class="thumbnail">
            <?php $imgSrc = isset($artist["LastfmArtist"]["image"]) ? $artist["LastfmArtist"]["image"] : "/img/placeholder.png"; ?>
            <?php echo $this->Html->link(
                        $this->Html->image($imgSrc, array("alt" => $artist["Artist"]["name"])),
                        array('controller' => 'artists', 'action' => 'view', $artist["Artist"]["slug"]),
                        array('escape' => false)
                ); ?>                    
            <h3><?php echo $this->Html->link($artist["Artist"]["name"], array('controller' => 'artists', 'action' => 'view', $artist["Artist"]["slug"])); ?></h3>
        </div>
        <?php if(count($artist["Albums"])) : ?>
        <ul class="recent-albums">
            <?php foreach($artist["Albums"] as $i => $album) : if($i >= 3) break;?>
            <li>
                <?php if(isset($album["image"])) : ?>
                <?php echo $this->Html->image($album["image"], array("alt" => $album["name"], "class" => "thumbnail")); ?>
                <?php endif; ?>
                <?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>
<ul class="tiled-list artists">
<?php foreach($artists as $artist) : ?>
    <li>
        <h3>
            <a class="thumbnail" href="<?php echo $this->Html->url(array('controller' => 'artists', 'action' => 'view', $artist["Artist"]["slug"])); ?>" <?php if(isset($artist["LastfmArtist"]["image"])){  echo 'style="background-image:url(/img/'.$artist["LastfmArtist"]["image"].');"'; } ?>>
                <span><?php echo $artist["Artist"]["name"]; ?></span>
            </a>
        </h3>

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
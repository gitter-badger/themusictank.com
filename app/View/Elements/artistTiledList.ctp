<ul class="tiled-list artists">
<?php foreach($artists as $artist) : ?>
    <li>
        <h3>
            <a class="thumbnail" href="<?php echo $this->Html->url(array('controller' => 'artists', 'action' => 'view', $artist["Artist"]["slug"])); ?>" style="background-image:url(<?php echo $this->App->getImageUrl($artist["LastfmArtist"], true); ?>);">
                <span><?php echo $artist["Artist"]["name"]; ?></span>
            </a>
        </h3>

        <?php if(count($artist["Albums"])) : ?>
        <ul class="recent-albums">
            <?php foreach($artist["Albums"] as $i => $album) : if($i >= 3) break;?>
            <li>
                <?php echo $this->Html->image($this->App->getImageUrl($artist["LastfmArtist"]), array("alt" => $album["name"], "class" => "thumbnail")); ?>
                <?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>
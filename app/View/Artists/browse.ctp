<h2><?php echo $title; ?></h2>

<section class="search">
    <?php echo $this->element('artistSearch', array("artistCategories" => $artistCategories)); ?>
</section>

<ul class="tiled-list artists">
<?php foreach($artists as $artist) : ?>
    <li>
        <?php if(isset($artist["LastfmArtist"]["image"])) : ?>
            <?php echo $this->Html->link(
                $this->Html->image($artist["LastfmArtist"]["image"], array("alt" => $artist["Artist"]["name"], "class" => "thumbnail")),
                array('controller' => 'artists', 'action' => 'view', $artist["Artist"]["slug"]),
                array('escape' => false)
            ); ?>
        <?php endif; ?>          
        <?php echo $this->Chart->getSmallPie("artist", $artist["Artist"]["slug"], $artist["ArtistReviewSnapshot"]); ?>
        <?php echo $this->Html->link($artist["Artist"]["name"], array('controller' => 'artists', 'action' => 'view', $artist["Artist"]["slug"])); ?>
        
        <?php if(count($artist["Albums"])) : ?>
            <ul class="recent-albums">
                <?php foreach($artist["Albums"] as $i => $album) : if($i >= 3) break;?>
                <li>
                    <?php if($album["image"]) echo $this->Html->image($album["image"], array("alt" => $album["name"], "class" => "thumbnail", "height" => 50)); ?>
                    <?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>

<div class="pagination">
    <?php echo $this->Paginator->numbers(); ?>
    <?php echo __("Page"); ?> <?php echo $this->Paginator->counter(); ?>
</div>
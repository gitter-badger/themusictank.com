<?php
    $isLogged = $this->Session->check('Auth.User.User.id');
?>
<div class="header-wrapper">
    <?php if(!is_null($album["image"])) : ?>
        <div class="cover-image blurred" style="background-image:url(<?php echo $this->App->getImageUrl($album, "blur"); ?>);"></div>
        <div class="cover-image clean" style="background-image:url(<?php echo $this->App->getImageUrl($album, "big"); ?>);"></div>
    <?php endif; ?>
    <i class="mask"></i>
</div>

<article class="container container-fluid">

    <header class="collapsed">

        <?php $img = $this->Html->image( $this->App->getImageUrl($album), array("alt" => $album["name"], "class" => "thumbnail")); ?>
        <?php echo $this->Html->link($img, array('controller' => 'albums', 'action' => 'view', $album["slug"]), array("escape" => false)); ?>

        <h1><?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?></h1>

        <h2><?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?></h2>

    </header>

    <div class="row content headerless">

        <p class="lead full-width"><?php echo __("Sorry for the inconvenience. The track and album details are currently being processed..."); ?></p>
        <div class="loading-wrap">
            <i class="fa fa-cog fa-spin fa-fw"></i>
        </div>

    </div>
</article>

<?php if ((int)$lastfmAlbum["lastsync"] > 0) : ?>
<script>$(function(){
    <?php if(isset($albumReviewSnapshot)) : ?>
        tmt.createPie(".everyone.piechart", [{"type" : "smile", "value" : <?php echo (int)$albumReviewSnapshot["liking_pct"]; ?>}, {"type" : "meh", "value" : <?php echo (int)$albumReviewSnapshot["neutral_pct"]; ?>}, {"type" : "frown", "value" : <?php echo (int)$albumReviewSnapshot["disliking_pct"]; ?>}], {key: "tanker chart-tanker"});
    <?php endif; ?>
});</script>
<?php endif; ?>

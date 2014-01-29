<?php
    $graphConfig = array(
        "containerSelector" => ".graph-" . $album["slug"] . " canvas",
        "curve_snapshot" => $albumReviewSnapshot["curve_snapshot"],
        "range_snapshot" => $albumReviewSnapshot["range_snapshot"],
        "subs_curve_snapshot" => isset($userAlbumReviewSnapshot) ? $userAlbumReviewSnapshot["curve_snapshot"] : null, 
        "subs_range_snapshot" => isset($userAlbumReviewSnapshot) ? $userAlbumReviewSnapshot["range_snapshot"] : null
    );
    
    $trackLength = 0;
    foreach($tracks as $track)
    {
        $trackLength += $track["duration"];
    }
    
    $isLogged = $this->Session->read("Auth.User.User.id");
?>
<section class="player chart album-chart <?php echo $isLogged ? 'logged' : 'not-logged' ?> graph-<?php echo $album["slug"]; ?>">
    <canvas></canvas>        
    <div class="tracks">
    <?php foreach($tracks as $track) : ?><div style="width:<?php echo ($track["duration"] / $trackLength) * 100; ?>%;"><span><?php echo $track["title"]; ?></span></div><?php endforeach; ?>
    </div>
    <ul class="legend">
        <li class="everyone">
            <label>
                <input type="checkbox" name="view" value="everyone" checked="checked" />
                <?php echo __("Everyone"); ?>
            </label>
        </li>
        <?php if($isLogged) : ?>
        <li class="friends">
            <label>
                <input type="checkbox" name="view" value="subs" checked="checked" />
                <?php echo __("People you are subscribed to"); ?>
            </label>
        </li>
        <?php endif; ?>
    </ul>    
    </div>     
<script>$(function(){
new tmt.graph(<?php echo json_encode($graphConfig); ?>).init();
});</script>
</section>
<?php
    $graphConfig = array(
        "containerSelector" => ".graph-" . $album["slug"] . " canvas",
        "curve_snapshot" => $albumReviewSnapshot["curve_snapshot"],
        "range_snapshot" => $albumReviewSnapshot["range_snapshot"],
        "subs_curve_snapshot" => isset($subsAlbumReviewSnapshot) && count($subsAlbumReviewSnapshot) ? $subsAlbumReviewSnapshot["curve_snapshot"] : null, 
        "subs_range_snapshot" => isset($subsAlbumReviewSnapshot) && count($subsAlbumReviewSnapshot) ? $subsAlbumReviewSnapshot["range_snapshot"] : null,
        "user_curve_snapshot" => isset($userAlbumReviewSnapshot) && count($userAlbumReviewSnapshot) ? $userAlbumReviewSnapshot["curve_snapshot"] : null, 
        "user_range_snapshot" => isset($userAlbumReviewSnapshot) && count($userAlbumReviewSnapshot) ? $userAlbumReviewSnapshot["range_snapshot"] : null
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
        <li class="you">
            <label>
                <input type="checkbox" name="view" value="user" checked="checked" />
                <?php echo __("You"); ?>
            </label>
        </li>
        <?php endif; ?>
    </ul> 
<script>$(function(){
new tmt.Graph(<?php echo json_encode($graphConfig); ?>);
});</script>
</section>
<?php
    $graphConfig = array(
        "containerSelector" => ".graph-" . $album["slug"] . " canvas",
        "curves" =>array(),
        "ranges" => array()
    );    
    
    if(isset($albumReviewSnapshot) && count($albumReviewSnapshot))
    {
        $graphConfig["curves"][ "tankers" ] = array(
            "label" => __("Everyone"),
            "data" => $albumReviewSnapshot["curve_snapshot"],
            "color" => "#999999"
        );         
        $graphConfig["ranges"][ "tankers" ] = array(
            "label" => __("Everyone"),
            "data" => $albumReviewSnapshot["range_snapshot"],
            "color" => "rgba(66, 66, 66,.4)"
        ); 
    }     
    
    if(isset($subsAlbumReviewSnapshot) && count($subsAlbumReviewSnapshot))
    {
        $graphConfig["curves"][ "subs" ] = array(
            "label" => __("Your subscriptions"),
            "data" => $subsAlbumReviewSnapshot["curve_snapshot"],
            "color" => "#4285f4"
        );         
        $graphConfig["ranges"][ "subs" ] = array(
            "label" => __("Your subscriptions"),
            "data" => $subsAlbumReviewSnapshot["range_snapshot"],
            "color" => "rgba(66, 133, 244, .4)"
        ); 
    }
    
    if(isset($userAlbumReviewSnapshot) && count($userAlbumReviewSnapshot))
    {
        $graphConfig["curves"][ "you" ] = array(
            "label" => __("You"),
            "data" => $userAlbumReviewSnapshot["curve_snapshot"],
            "color" => "rgb(90, 20, 244)"
        );         
        $graphConfig["ranges"][ "you" ] = array(
            "label" => __("You"),
            "data" => $userAlbumReviewSnapshot["range_snapshot"],
            "color" => "rgba(90, 20, 244, .4)"            
        ); 
    }   
    
    $albumLength = 0;
    foreach($tracks as $track)
    {
        $albumLength += $track["duration"];
    }
    
    $isLogged = $this->Session->read("Auth.User.User.id");
?>
<section class="player chart album-chart <?php echo $isLogged ? 'logged' : 'not-logged' ?> graph-<?php echo $album["slug"]; ?>" style="height:1000px;">
    <canvas></canvas>
    <ul class="labels">
        <li><?php echo __("Dislike"); ?></li>
        <li><?php echo __("Neutral"); ?></li>
        <li><?php echo __("Liking"); ?></li>
    </ul>
    <div class="tracks">
        <?php foreach($tracks as $track) : ?>
            <div style="height:<?php echo ($track["duration"] / $albumLength) * 100; ?>%;">
                <?php echo $track["title"]; ?>
            </div>
        <?php endforeach; ?>
    </div>

        
    <?php if(count($graphConfig["curves"]) > 0) : ?>    
    <ul class="legend">      
        <?php foreach($graphConfig["curves"] as $key => $curveInfo) : ?>
        <li class="<?php echo $key; ?>">
            <label>
                <input type="checkbox" name="view" value="<?php echo $key; ?>" checked="checked" />
                <?php echo $curveInfo["label"]; ?>
            </label>
        </li>
        <?php endforeach; ?>
    </ul> 
    <?php endif; ?>
<script>$(function(){
new tmt.Graph(<?php echo json_encode($graphConfig); ?>);
});</script>
</section>
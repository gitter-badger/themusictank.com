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

    $lengthClass = "";
    if($albumLength > 60*45) {
        $lengthClass = "long";
    }elseif($albumLength < 60*30) {
        $lengthClass = "short";
    }

    $isLogged = $this->Session->read("Auth.User.User.id");
?>

<style>
.axis path, .axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
}

.area {
  fill: lightsteelblue;
}

.line {
  fill: none;
  stroke: steelblue;
  stroke-width: 1.5px;
}

.dot {
  fill: white;
  stroke: steelblue;
  stroke-width: 1.5px;
}
</style>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script>

var jsonData = <?php echo json_encode($graphConfig); ?>;
var data = d3.range(<?php echo $albumLength; ?>).map(function(i) {

	if(jsonData.curves.you.data[i]) {
		return {x:i, x: jsonData.curves.you.data[i].avg};
	}

  return {x: i, y: 0.5};
});

var margin = {top: 20, right: 20, bottom: 30, left: 40},
    width = 960 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom;

var x = d3.scale.linear()
    .range([0, width]);

var y = d3.scale.linear()
    .range([height, 0]);

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left");

var line = d3.svg.line()
    .defined(function(d) { return d.y != null; })
    .x(function(d) { return x(d.x); })
    .y(function(d) { return y(d.y); });

var area = d3.svg.area()
    .defined(line.defined())
    .x(line.x())
    .y1(line.y())
    .y0(y(0));

var svg = d3.select("body").append("svg")
    .datum(data)
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

svg.append("path")
    .attr("class", "area")
    .attr("d", area);

svg.append("g")
    .attr("class", "x axis")
    .attr("transform", "translate(0," + height + ")")
    .call(xAxis);

svg.append("g")
    .attr("class", "y axis")
    .call(yAxis);

svg.append("path")
    .attr("class", "line")
    .attr("d", line);

svg.selectAll(".dot")
    .data(data.filter(function(d) { return d.y; }))
  .enter().append("circle")
    .attr("class", "dot")
    .attr("cx", line.x())
    .attr("cy", line.y())
    .attr("r", 3.5);



</script>



<section class="timeline-chart album-chart <?php echo $lengthClass; ?> <?php echo $isLogged ? 'logged' : 'not-logged' ?> graph-<?php echo $album["slug"]; ?>">
    <div class="viewport">
        <canvas></canvas>

        <div class="tracks">
            <?php foreach($tracks as $track) : ?>
                <div style="width:<?php echo ($track["duration"] / $albumLength) * 100; ?>%;">
                    <?php echo $this->Html->link($track["title"], array('controller' => 'tracks', 'action' => 'view', $track["slug"])); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
            $i = 0;
            $minutePrecision = (60*5);
            $nbIntervals = round((int)$album["duration"] / $minutePrecision);
        ?>
        <ul class="times">
        <?php while($i <  $nbIntervals) :  $timestamp = $i * $minutePrecision; ?>
            <li <?php if($i+1 != $nbIntervals) : ?>style="width:<?php echo 100 / $nbIntervals; ?>%;"<?php endif; ?>><?php echo date("i:s", $timestamp); ?></li>
        <?php $i++; endwhile; ?>
        </ul>
        <script>$(function(){new tmt.Graph(<?php echo json_encode($graphConfig); ?>);});</script>
    </div>

     <?php if(count($graphConfig["curves"]) > 0) : ?>
        <ul class="curves">
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
</section>

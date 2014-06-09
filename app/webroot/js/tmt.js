
var tmt = window.tmt || {};

$(function() {

    // Add remove follower
    var onFollowClick = function(event){
        var $el = $(this);
        event.preventDefault();
        $.ajax($el.attr("href")).done(function(data)
        {
            var parent = $el.parent();
            parent.html(data);
            parent.find("a.follow, a.unfollow").on("click", onFollowClick);
        });
    };
    $("a.follow, a.unfollow").on("click", onFollowClick);


    // search box
    var artistsSearch = new Bloodhound({
            name : 'artists',
            datumTokenizer: function(d) { return Bloodhound.tokenizers.obj.whitespace(d.artist); },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: '/ajax/artistssearch/?q=%QUERY'
        }),
        albumsSearch = new Bloodhound({
            name : 'albums',
            datumTokenizer: function(d) { return Bloodhound.tokenizers.obj.whitespace(d.album); },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: '/ajax/albumssearch/?q=%QUERY'
        }),
        tracksSearch = new Bloodhound({
            name : 'tracks',
            datumTokenizer: function(d) { return Bloodhound.tokenizers.obj.whitespace(d.track); },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: '/ajax/trackssearch/?q=%QUERY'
        });

    artistsSearch.initialize();
    albumsSearch.initialize();
    tracksSearch.initialize();


    var searchBox = $('.typeahead');

    searchBox.on("typeahead:selected", function(e, data, section) {
        document.location = "/" + [section, 'view', data.slug].join("/");
        e.preventDefault();
    });

    searchBox.typeahead({
          minLength: 3,
          highlight: true,
        },
        [{
            name: 'artists',
            displayKey: 'artist',
            source: artistsSearch.ttAdapter(),
            templates: {
                header: '<h3>Artists</h3>',
                suggestion: function(data) { return ["<p>", data.artist, "</p>"].join(""); }
            }
        },
        {
            name: 'albums',
            displayKey: 'album',
            source: albumsSearch.ttAdapter(),
            templates: {
                header: '<h3>Albums</h3>',
                suggestion: function(data) { return ["<p>", data.album, " by ", data.artist, "</p>"].join(""); }
            }
        },
        {
            name: 'tracks',
            displayKey: 'track',
            source: tracksSearch.ttAdapter(),
            templates: {
                header: '<h3>Tracks</h3>',
                suggestion: function(data) { return ["<p>", data.track, " from ", data.album, "</p>"].join(""); }
            }
        }
    ]);

    // Expander util
    var box = $(".expandable");
    if(box.length > 0)
    {
        $(".expandable").each(function(){
            var parent = $(this),
                height = box.innerHeight(),
                maxHeight = 200,
                tpl = '<ul class="expander">' +
                    '<li class="more"><i class="fa fa-plus-circle"></i> More</li>' +
                    '<li class="less"><i class="fa fa-minus-circle"></i> Less</li>' +
                '</ul>';

            if(height > maxHeight)
            {
                parent.addClass("collapsed");
                parent.append(tpl);
                parent.find(".expander li").click(function(event){
                    var el = $(event.target);
                    if(el.hasClass("more")) parent.removeClass("collapsed");
                    if(el.hasClass("less")) parent.addClass("collapsed");
                });
            }
        });
    }

    // Notifier util
    var box = $(".notifier");
    if(box.length > 0)
    {
        function markAsRead()
        {
            $.ajax({
                dataType : "html",
                url : "/ajax/okstfu/",
                success : function(data) { box.html(data); }
            });
            return false;
        }

        function getNotifications()
        {
            $.ajax({
                dataType : "html",
                url : "/ajax/whatsup/",
                success : function(data) {
                    box.html(data);
                    $(".notifier li.mark a").click(markAsRead);
                    setTimeout(getNotifications, 1.5 * 60 * 1000);
                }
            });
        }

        setTimeout(getNotifications, 300);
    }

    // map util
    var box = $("#mapCanvas");
    if(box.length > 0)
    {
        var mapOptions = {
            center: new google.maps.LatLng(45.5000, -73.5667),
            zoom: 8,
            disableDefaultUI: true,
            draggable: false,
            disableDoubleClickZoom: false,
            scrollwheel : false,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        },
        map = new google.maps.Map(box.get(0), mapOptions);

    }

    // Automate post loading of elements
	$("*[data-post-load]").each(function(){
		var $this = $(this);
		$this.load($this.attr("data-post-load"));
	});


	tmt.createPie = function(whereClass, jsonData, details) {
	 var w = 200,                        //width
	    h = 200,                            //height
	    r = 100,                            //radius
	    color = d3.scale.category20c();     //builtin range of colors

    data = jsonData;

var vis = d3.select(whereClass)
        .append("svg:svg")              //create the SVG element inside the <body>
        .data([data])                   //associate our data with the document
            .attr("width", w)           //set the width and height of our visualization (these will be attributes of the <svg> tag
            .attr("height", h)
        .append("svg:g")                //make a group to hold our pie chart
            .attr("transform", "translate(" + r + "," + r + ")")    //move the center of the pie chart from 0, 0 to radius, radius

    var arc = d3.svg.arc()              //this will create <path> elements for us using arc data
        .outerRadius(r);


    var pie = d3.layout.pie()           //this will create arc data for us given a list of values
        .value(function(d) {return d.value; });    //we must tell it out to access the value of each element in our data array

    var arcs = vis.selectAll("g.slice")     //this selects all <g> elements with class slice (there aren't any yet)
        .data(pie)                          //associate the generated pie data (an array of arcs, each having startAngle, endAngle and value properties)
        .enter()                            //this will create <g> elements for every "extra" data element that should be associated with a selection. The result is creating a <g> for every object in the data array
            .append("svg:g")                //create a group to hold each slice (we will have a <path> and a <text> element associated with each slice)
                .attr("class", "slice");    //allow us to style things in the slices (like text)

        arcs.append("svg:path")
                .attr("fill", function(d, i) { return color(i); } ) //set the color for each slice to be chosen from the color function defined above
                .attr("d", arc);                                    //this creates the actual SVG path using the associated data (pie) with the arc drawing function

            arcs.append("svg:text")                                            //add a label to each slice
        	    .attr("transform", function(d) {                    //set the label's origin to the center of the arc
                //we have to make sure to set these before calling arc.centroid
                d.innerRadius = 0;
                d.outerRadius = r;
                return "translate(" + arc.centroid(d) + ")";        //this gives us a pair of coordinates like [50, 50]
            })
            .attr("text-anchor", "middle")                          //center the text on it's origin
            .text(function(d, i) { return data[i].type; });        //get the label from our original data array

	};


    tmt.createLine = function (svg, jsonData, details) {
    	var margin = {top: 20, right: 20, bottom: 30, left: 40},
		    width = 960 - margin.left - margin.right,
		    height = 500 - margin.top - margin.bottom;

		var data = d3.range(details.total).map(function(i) {

			if (jsonData[i]) {
		  		return {x: i, y: parseFloat(jsonData[i].avg)};
		  	}
		  	return {x:i, y: 0.5};
		});

		var x = d3.scale.linear()
			.domain([0, data[data.length-1].x])
		    .range([0, width]);

		var y = d3.scale.linear()
			.domain([0,1])
		    .range([height, 0]);

		var line = d3.svg.line()
		    .defined(function(d) { return d.y != null; })
		    .x(function(d) { return x(d.x); })
		    .y(function(d) { return y(d.y); });

		svg.append("path")
		    .attr("class", "line " + details.key)
		    .attr("d", line);

		svg.selectAll(".dot")
		    .data(data.filter(function(d) { return d.y; }))
		  .enter().append("circle")
		    .attr("class", "dot " + details.key)
		    .attr("cx", line.x())
		    .attr("cy", line.y())
		    .attr("r", 3.5);
    };

    tmt.createRange = function (svg, jsonData, details) {
		var data = d3.range(details.total).map(function(i) {
/*
			var min = null,
				max = null;

			if(jsonData.min && jsonData.min[i]) {
				min = jsonData[i].min;
			}
			if(jsonData.max && jsonData.max[i]) {
				max = jsonData[i].max;
			}*/
			if(jsonData[i]) {
		  		return {x: i, y: parseFloat(jsonData[i].min), x1: i, y1 : parseFloat(jsonData[i].max)};
		  	}
		  	return {x:i, y:0.5, x1:i, y1:0.5}
		});


		var margin = {top: 20, right: 20, bottom: 30, left: 40},
		    width = 960 - margin.left - margin.right,
		    height = 500 - margin.top - margin.bottom;

		var x = d3.scale.linear()
			.domain([0, data[data.length-1].x])
		    .range([0, width]);

		var y = d3.scale.linear()
			.domain([0,1])
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

		var bLine = d3.svg.line()
		    .defined(function(d) { return d.y1 != null; })
		    .x(function(d) { return x(d.x1); })
		    .y(function(d) { return y(d.y1); });

		var area = d3.svg.area()
		    .defined(line.defined())
		    .x(line.x())
		    .y1(bLine.y())
		    .y0(line.y());

		svg.datum(data)
		    .attr("width", width + margin.left + margin.right)
		    .attr("height", height + margin.top + margin.bottom)
		  .append("g")
		    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

		svg.append("path")
		    .attr("class", "area " + details.key)
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

		svg.append("path")
		    .attr("class", "line")
		    .attr("d", bLine);

    };

});

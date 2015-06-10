// This is a manifest file that'll be compiled into application.js, which will include all the files
// listed below.
//
// Any JavaScript/Coffee file within this directory, lib/assets/javascripts, vendor/assets/javascripts,
// or vendor/assets/javascripts of plugins, if any, can be referenced here using a relative path.
//
// It's not advisable to add code directly here, but if you do, it'll appear at the bottom of the
// compiled file.
//
// Read Sprockets README (https://github.com/sstephenson/sprockets#sprockets-directives) for details
// about supported directives.
//
//= require jquery
//
//= require bootstrap/button
//= require bootstrap/collapse
//= require bootstrap/dropdown
//= require bootstrap/modal
//
//= require bower_components/typeahead.js/dist/typeahead.bundle.min.js
//= require tmt
//= require search
//= require bugreport
//= require scrolling
//= require googlemap
//= require previewer
//= require reviewer

$(function() {

    // Frequent calls of DOM objects should be cached here
    var _domcache = {
        "window" : $(window),
        ".navbar" : $('.navbar'),
        ".breadcrumbs" : $('.breadcrumbs'),
        ".header-wrapper" : $('.header-wrapper'),
        ".header-wrapper .mask" : $('.header-wrapper .mask'),
        ".header-wrapper div" : $('.header-wrapper div'),
        ".header-wrapper div.clean" : $('.header-wrapper div.clean')
    }

    // Automate post loading of elements
    $("*[data-post-load]").each(function(){
        var $this = $(this);
        $this.load($this.attr("data-post-load"));
    });


    tmt.pieGraph = function(key, jsonData) {
        if (jsonData) {
            $('.piechart.' + key).each(function(){
                _pie($(this).append("<canvas>"), jsonData);
            });
        }
    };

    tmt.lineGraph = function(key, jsonData) {
        if (jsonData) {
            $('.timeline .' + key + ' canvas.graph').each(function(){
                _line($(this), jsonData);
            });
        }
    };

    tmt.rangeGraph = function(key, jsonData) {
        if (jsonData) {
            $('.timeline .' + key + ' canvas.graph').each(function(){
                _range($(this), jsonData);
            });
        }
    };

    tmt.waveform = function(key, jsonData) {
        if (jsonData) {
            $('.timeline .' + key + ' canvas.graph').each(function(){
                _wave($(this), jsonData);
            });
        }
    }

    function _pie(el, data) {
        var canvas = el.find("canvas").get(0),
            context = canvas.getContext("2d"),
            height = el.height(),
            width = el.width();

        canvas.width = width;
        canvas.height = height;

        // Start by drawing a full neutral area. It will also be used
        // as the basis of the circle background. The other areas will overlap
        // and all will be fine.
        context.beginPath();
        context.arc(height / 2, width / 2, width, 0, 2 * Math.PI, false);
        context.fillStyle = '#000000';
        context.fill();

        var startAngle = 0;
        if (data.liking_pct > 0) {
            context.beginPath();
            context.arc(height / 2, width / 2, width, 0, 360 * data.liking_pct, false);
            context.fillStyle = 'green';
            context.fill();
            startAngle = 360 * data.liking_pct;
        }

        if (data.disliking_pct > 0) {
            context.beginPath();
            context.arc(height / 2, width / 2, width, startAngle, 360 * data.disliking_pct, false);
            context.fillStyle = 'red';
            context.fill();
        }

    };

    function _line(el, data) {
        var canvas = el.get(0),
            context = canvas.getContext("2d"),
            height = el.height(),
            width = el.width();

        canvas.width = width;
        canvas.height = height;

        context.beginPath();
        context.moveTo(0, height / 2);

        for(var i = 0, len = data.length; i < len; i++) {
            // i forgot the data structure.
        }

        // Stick it to the right and stroke the line.
        context.lineTo(width, height / 2);
        context.strokeStyle = '#333';
        context.strokeWidth = '1.2';
        context.stroke();
    };

    function _wave(el, data) {
        var canvas = el.get(0),
            context = canvas.getContext("2d"),
            height = el.height(),
            half = height / 2,
            width = el.width();

        canvas.width = width;
        canvas.height = height;

        for(var i = 0, len = data.length, boxWidth = width / len, wave, waveHeight; i < len; i++) {
            wave =  Math.abs(128 - data[i]);
            waveHeight = (wave * 100 / 128) * half;

            context.beginPath();
            // x, y, width, height
            context.rect(i * boxWidth, waveHeight - half, boxWidth, half + waveHeight);
            context.fillStyle = 'rgba(0,0,0.4)';
            context.fill();
        }
    }

    function _range(el, data) {

    }


/*
    tmt.waveform = function(svg, jsonData, details) {
        if(jsonData && jsonData.length > 0) {
            var margin = {top: 20, right: 20, bottom: 30, left: 40},
                width = svg[0][0].offsetWidth  - margin.left - margin.right,
                height = svg[0][0].offsetHeight  - margin.top - margin.bottom;
            var boxWidth =  width / jsonData.length;
            var pctData = [];
            for(var i = 0, len = jsonData.length; i < len; i++) {
                svg.append("line")
                    .attr("x1", boxWidth*i)
                    .attr("y1", height * jsonData[i][0] / 100)
                    .attr("x2", boxWidth*i)
                    .attr("y2", height * jsonData[i][1] / 100);
            }
        }
    };
    tmt.createLine = function (svg, jsonData, details) {
        var margin = {top: 20, right: 20, bottom: 30, left: 40},
            width = svg[0][0].offsetWidth  - margin.left - margin.right,
            height = svg[0][0].offsetHeight  - margin.top - margin.bottom;
    if(jsonData.length === 0) {
return;
    }
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
    if(jsonData.length === 0) {
return;
    }
        var data = d3.range(details.total).map(function(i) {
            if(jsonData[i]) {
                return {x: i, y: parseFloat(jsonData[i].min), x1: i, y1 : parseFloat(jsonData[i].max)};
            }
            return {x:i, y:0.5, x1:i, y1:0.5}
        });
        var margin = {top: 20, right: 20, bottom: 30, left: 40},
            width = svg[0][0].offsetWidth - margin.left - margin.right,
            height = svg[0][0].offsetHeight - margin.top - margin.bottom;
// Formatters for counts and times (converting numbers to Dates).
var formatCount = d3.format(",.0f"),
    formatTime = d3.time.format("%H:%M"),
    formatMinutes = function(d) { return formatTime(new Date(2012, 0, 1, 0, d)); };
        var x = d3.scale.linear()
            .domain([0, data[data.length-1].x])
            .range([0, width]);
        var y = d3.scale.linear()
            .domain([0,1])
            .range([height, 0]);
        var xAxis = d3.svg.axis()
            .scale(x)
            .orient("bottom")
            .tickFormat(formatMinutes);
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
*/

});


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
    
    // Automagic charting
    tmt.pie = function(key, d)
    {   
        $("#pie-" + key).highcharts({                
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            plotOptions: {
                pie: {
                    allowPointSelect: false,
                    cursor: 'pointer',
                    dataLabels: {enabled: false},
                    showInLegend: false
                }
            },
            title : null,
            series: [{
                type: 'pie',
                data: d
            }]
        });
    };        
        
    tmt.chart = function(key, d)
    {   
        $("#chart-" + key).highcharts({
        series: [{
		    	name: 'Groove',
		    	data: d.groove,
		    	zIndex: 1,
		    	marker: {
		    		fillColor: 'white',
		    		lineWidth: 2,
		    		lineColor: Highcharts.getOptions().colors[0]
		    	}
		    }, {
		        name: 'Range',
		        data: d['range'],
		        type: 'arearange',
		        lineWidth: 0,
		    	linkedTo: ':previous',
		    	color: Highcharts.getOptions().colors[0],
		    	fillOpacity: 0.3,
		    	zIndex: 0
		    }]
        });
    };

});
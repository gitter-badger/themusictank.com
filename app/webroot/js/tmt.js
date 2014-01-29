
var tmt = window.tmt || {};
    
$(function(){
        
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
        
        
    // Expander util    
    var box = $(".expandable");
    if(box.length > 0)
    {
        $(".expandable").each(function(){
            var parent = $(this),
                height = box.innerHeight(),
                maxHeight = 200,
                tpl = '<ul class="expander">' +
                    '<li class="more">more</li>' +
                    '<li class="less">less</li>' +
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
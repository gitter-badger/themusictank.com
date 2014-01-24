(function() {
    var tmt = window.tmt;
    
    var PADDING = 20,
        MARGIN = 5;
    
    
    var graph = tmt.graph = function(config)
    {
        this.config = config;
    };
    
    graph.prototype = {
      
        init : function() 
        {
            this.config.container   = {ref : $(this.config.containerSelector)}; 
            
            this.getContextSizes();
            this.draw();
        },
                
        draw : function()
        {
            this.drawJoints();
            this.drawCircles();
            this.drawTimeLabels();
        },
        
        getContextSizes : function()
        {
            if(this.config.container)
            {
                var ref = this.config.container.ref,
                    node = ref.get(0),
                    border = PADDING + MARGIN;
                  
                this.config.sizes = {
                    top     : border,
                    left    : border,
                    height  : ref.height() - border,
                    width   : ref.width() - border
                };                
                
                node.height = ref.height();
                node.width = ref.width();
                this.config.context = node.getContext("2d");                
            }            
        },
        
        drawTimeLabels : function()
        {
            var periods = Math.ceil(this.config.trackDuration / 30),
                period = 0,
                context = this.config.context,
                distanceBetweenPoints = this.config.sizes.width / periods,
                currentPoint    = PADDING + MARGIN;
            
            for( ; period < periods; period++)
            {
                var time = period * 30;
                var mins = Math.floor(time / 60);
                var secs = time - (mins * 60);

                if(secs === 0) secs = "00";
                                
                context.beginPath();
                context.fillStyle = '#333';
                context.fillText(mins + ":" + secs, currentPoint, this.config.sizes.height + PADDING);                
                currentPoint += distanceBetweenPoints;                
            }
            
        },        
        
        drawJoints : function()
        {   
            var context = this.config.context,
                i = 0,
                len = this.config.curve_snapshot.length,
                dotSize = 4.5,
                currentPoint    = dotSize / 2,
                distanceBetweenPoints = this.config.sizes.width / len,
                yPos, prevYPos = 0;
            
            for( ; i < len; i++, yPos = 0)
            {                   
                
                if(this.config.curve_snapshot[i])
                {
                    yPos = this.config.sizes.height * (1-this.config.curve_snapshot[i][1]);
                }       
                
                if(prevYPos > 0 && yPos > 0)
                {
                    context.beginPath();
                    context.moveTo(currentPoint - distanceBetweenPoints, prevYPos);
                    context.lineTo(currentPoint, yPos);
                    context.lineWidth = 1;
                    context.stroke();
                }
                
                prevYPos = yPos;
                currentPoint += distanceBetweenPoints;
            }            
        },
        
        drawCircles : function()
        {
            var context = this.config.context,
                i = 0,
                len = this.config.curve_snapshot.length,
                dotSize = 4.5,
                currentPoint    = dotSize / 2,
                twopi = 2 * Math.PI,
                distanceBetweenPoints = this.config.sizes.width / len,
                yPos;
            
            for( ; i < len; i++)
            {  
                if(this.config.curve_snapshot[i])
                {
                    yPos = this.config.sizes.height * (1-this.config.curve_snapshot[i][1]);                
                    context.beginPath();
                    context.arc(currentPoint, yPos, dotSize, 0, twopi, false);
                    context.fillStyle = 'green';
                    context.fill();
                    context.lineWidth = .75;
                    context.strokeStyle = '#003300';
                    context.stroke();                       
                }   
                currentPoint += distanceBetweenPoints;
            }
        }
        
    };
    
})();
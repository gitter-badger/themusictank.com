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
            this.drawGridLines();
            this.drawTimeLabels();
            this.drawJoints();
            this.drawCircles();
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
        
        drawGridLines : function()
        {
            var height = this.config.sizes.height - MARGIN,
                context = this.config.context;
            

            context.beginPath();
            context.moveTo(PADDING, height / 2);
            context.lineTo(this.config.sizes.width, height / 2);
            context.lineWidth = 1;
            context.strokeStyle = '#dedede';
            context.stroke();
            
            context.beginPath();
            context.moveTo(PADDING,  PADDING);
            context.lineTo(this.config.sizes.width, PADDING);
            context.lineWidth = 1;
            context.strokeStyle = '#dedede';
            context.stroke();
            context.beginPath();
            
            context.moveTo(PADDING, this.config.sizes.height - PADDING);
            context.lineTo(this.config.sizes.width, this.config.sizes.height - PADDING);
            context.lineWidth = 1;
            context.strokeStyle = '#dedede';
            context.stroke();
                
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
            

            context.beginPath();
            context.fillStyle = '#333';
            context.fillText("0", MARGIN, this.config.sizes.height / 2);

            context.beginPath();
            context.fillStyle = '#333';
            context.fillText("+1", MARGIN, MARGIN + PADDING);

            context.beginPath();
            context.fillStyle = '#333';
            context.fillText("-1", MARGIN, this.config.sizes.height - PADDING);
                
            
        },        
        
        drawJoints : function()
        {   
            var context = this.config.context,
                i = 0,
                len = this.config.curve_snapshot.length,
                dotSize = 4.5,
                currentPoint    = (dotSize / 2) + MARGIN + PADDING,
                distanceBetweenPoints = this.config.sizes.width / len,
                height = this.config.sizes.height - MARGIN,
                yPos, prevYPos = 0;
            
            for( ; i < len; i++, yPos = 0)
            {                   
                
                if(this.config.curve_snapshot[i])
                {
                    yPos = height * (1-this.config.curve_snapshot[i][1]);
                    yPos += MARGIN;
                }       
                
                if(prevYPos > 0 && yPos > 0)
                {
                    context.beginPath();
                    context.moveTo(currentPoint - distanceBetweenPoints, prevYPos - (dotSize / 2));
                    context.lineTo(currentPoint, yPos - (dotSize / 2));
                    context.lineWidth = 1;
                    context.strokeStyle = '#003300';
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
                currentPoint    = (dotSize / 2) + MARGIN + PADDING,
                height = this.config.sizes.height - MARGIN,
                twopi = 2 * Math.PI,
                distanceBetweenPoints = this.config.sizes.width / len,
                yPos;
            
            for( ; i < len; i++)
            {  
                if(this.config.curve_snapshot[i])
                {
                    yPos = height * (1-this.config.curve_snapshot[i][1]);  
                    yPos += MARGIN;   
                    yPos -= dotSize;
                    context.beginPath();
                    context.arc(currentPoint, yPos , dotSize, 0, twopi, false);
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
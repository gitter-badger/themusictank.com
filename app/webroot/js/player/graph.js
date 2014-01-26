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
                    height  : ref.height(),
                    width   : ref.width()
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
            context.moveTo(10, this.config.sizes.height / 2);
            context.lineTo(this.config.sizes.width, height / 2);
            context.lineWidth = .5;
            context.strokeStyle = '#aaa';
            context.stroke();
            
            context.beginPath();
            context.moveTo(0,  0);
            context.lineTo(this.config.sizes.width, 0);
            context.lineWidth = .5;
            context.strokeStyle = '#aaa';
            context.stroke();
            context.beginPath();
            
            context.moveTo(0, this.config.sizes.height);
            context.lineTo(this.config.sizes.width, this.config.sizes.height);
            context.lineWidth = .5;
            context.strokeStyle = '#aaa';
            context.stroke();
                
        },
        
        drawTimeLabels : function()
        {
            var context = this.config.context;

            context.beginPath();
            context.fillStyle = '#aaa';
            context.textBaseline = 'middle';
            context.fillText("0", 0, this.config.sizes.height / 2);

            context.beginPath();
            context.fillStyle = '#aaa';
            context.textBaseline = 'top';
            context.fillText("+1", 0, 5);

            context.beginPath();
            context.fillStyle = '#aaa';
            context.textBaseline = 'bottom';
            context.fillText("-1", 0, this.config.sizes.height - 5);
        },        
        
        drawJoints : function()
        {   
            var context = this.config.context,
                i = 0,
                len = this.config.curve_snapshot.length,
                dotSize = 4,
                currentPoint    = (dotSize / 2) + 15,
                distanceBetweenPoints = (this.config.sizes.width -30) / len,
                height = this.config.sizes.height,
                yPos, prevYPos = 0;
            
            for( ; i < len; i++, yPos = 0)
            {                   
                
                if(this.config.curve_snapshot[i])
                {
                    yPos = height * (1-this.config.curve_snapshot[i][1]);
                }       
                
                if(prevYPos > 0 && yPos > 0)
                {
                    context.beginPath();
                    context.moveTo(currentPoint - distanceBetweenPoints- (dotSize / 2), prevYPos);
                    context.lineTo(currentPoint - (dotSize / 2), yPos);
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
                dotSize = 4,
                currentPoint    = 15,
                height = this.config.sizes.height,
                twopi = 2 * Math.PI,
                distanceBetweenPoints = (this.config.sizes.width - 30) / len,
                yPos;
            
            for( ; i < len; i++)
            {  
                if(this.config.curve_snapshot[i])
                {
                    yPos = height * (1-this.config.curve_snapshot[i][1]);  
                    //yPos += dotSize;
                    context.beginPath();
                    context.arc(currentPoint, yPos , dotSize, 0, twopi, false);
                    context.fillStyle = 'green';
                    context.fill();
                    context.lineWidth = .5;
                    context.strokeStyle = '#003300';
                    context.stroke();                       
                }   
                currentPoint += distanceBetweenPoints;
            }
        }
        
    };
    
})();
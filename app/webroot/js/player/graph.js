(function() {
    var tmt = window.tmt;
    
    var PADDING = 20,
        MARGIN = 5;
    
    
    var graph = tmt.graph = function(config)
    {
        this.config = config;
        return this;
    };
    
    graph.prototype = {
      
        init : function() 
        {
            this.config.container   = {ref : $(this.config.containerSelector)}; 
            this.config.container.ref.parent(".player").find(".legend input[type=checkbox]").change($.proxy(_onViewToggle, this));
            
            this.config.display = {
                "everyone" : (this.config.curve_snapshot),
                "subs" : (this.config.subs_curve_snapshot),
                "user" : (this.config.user_curve_snapshot)
            };
            
            this.getContextSizes();
            this.draw();
        },
                
        draw : function()
        {
            this.drawGridLines();
            this.drawTimeLabels();
                
            if(this.config.display.everyone && this.config.range_snapshot)
            {
                _drawRange.call(this, this.config.range_snapshot, "avg", "rgba(66, 66, 66,.4)");
            }            
            
            if(this.config.display.subs && this.config.subs_range_snapshot)
            {
                _drawRange.call(this, this.config.subs_range_snapshot, "avg", "rgba(66, 133, 244,.4)");         
            }
            
            if(this.config.display.user && this.config.user_range_snapshot)
            {
                _drawRange.call(this, this.config.user_range_snapshot, "avg", "rgba(90, 20, 244,.4)");         
            }
            
            if(this.config.display.everyone && this.config.curve_snapshot)
            {
                _drawJoints.call(this, this.config.curve_snapshot, "avg", '#999999');
                _drawCircles.call(this, this.config.curve_snapshot, "avg", '#999999');
            }
                        
            if(this.config.display.subs && this.config.subs_curve_snapshot)
            {
                _drawJoints.call(this, this.config.subs_curve_snapshot, "avg", '#4285f4');
                _drawCircles.call(this, this.config.subs_curve_snapshot, "avg", '#4285f4');                
            }  
            
            if(this.config.display.user && this.config.user_range_snapshot)
            {
                _drawJoints.call(this, this.config.user_range_snapshot, "avg", "rgb(90, 20, 244)");
                _drawCircles.call(this, this.config.user_range_snapshot, "avg", "rgb(90, 20, 244)");                
            }
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
            context.lineWidth = .5;
            context.strokeStyle = '#aaa';
            context.shadowOffsetX = 0;
            context.shadowOffsetY = 0;
            context.shadowBlur = 0;
            context.moveTo(10, this.config.sizes.height / 2);
            context.lineTo(this.config.sizes.width, height / 2);
            context.stroke();
            context.closePath();
            
            context.beginPath();
            context.lineWidth = .5;
            context.strokeStyle = '#aaa';
            context.shadowOffsetX = 0;
            context.shadowOffsetY = 0;
            context.shadowBlur = 0;
            context.moveTo(0,  0);
            context.lineTo(this.config.sizes.width, 0);
            context.stroke();
            context.closePath();
            
            context.beginPath();            
            context.lineWidth = .5;
            context.strokeStyle = '#aaa';
            context.shadowOffsetX = 0;
            context.shadowOffsetY = 0;
            context.shadowBlur = 0;
            context.moveTo(0, this.config.sizes.height);
            context.lineTo(this.config.sizes.width, this.config.sizes.height);
            context.stroke();
            context.closePath();
                
        },
        
        drawTimeLabels : function()
        {
            var context = this.config.context;

            context.beginPath();
            context.fillStyle = '#aaa';
            context.textBaseline = 'middle';
            context.shadowOffsetX = 0;
            context.shadowOffsetY = 0;
            context.shadowBlur = 0;
            context.fillText("0", 0, this.config.sizes.height / 2);
            context.closePath();

            context.beginPath();
            context.fillStyle = '#aaa';
            context.textBaseline = 'top';
            context.shadowOffsetX = 0;
            context.shadowOffsetY = 0;
            context.shadowBlur = 0;
            context.fillText("+1", 0, 5);
            context.closePath();

            context.beginPath();
            context.fillStyle = '#aaa';
            context.textBaseline = 'bottom';
            context.shadowOffsetX = 0;
            context.shadowOffsetY = 0;
            context.shadowBlur = 0;
            context.fillText("-1", 0, this.config.sizes.height - 5);
            context.closePath();
        }        
        
    };
    
    function _onViewToggle(evt)
    {
        var el = $(evt.target);        
        this.config.display[el.val()] = el.is(":checked");
        this.config.context.clearRect (0, 0, this.config.sizes.width, this.config.sizes.height);
        this.draw();
    };


    function _drawCircles (datasource, which, color)
    {
        var context = this.config.context,
            i = 0,
            len = datasource.length,
            dotSize = 4,
            currentPoint    = 15,
            height = this.config.sizes.height,
            twopi = 2 * Math.PI,
            distanceBetweenPoints = (this.config.sizes.width - 30) / len,
            yPos;

        for( ; i < len; i++)    
        {  
            if(datasource[i])
            {
                yPos = height * (1-datasource[i][which]);  
                //yPos += dotSize;
                context.beginPath();
                context.fillStyle = color;
                context.lineWidth = .5;
                context.strokeStyle = color;
                context.shadowColor = "rgba( 0, 0, 0, 0.3 )";
                context.shadowOffsetX = 1;
                context.shadowOffsetY = 1;
                context.shadowBlur = 3;
                context.arc(currentPoint, yPos , dotSize, 0, twopi, false);
                context.fill();
                context.stroke();   
                context.closePath();                    
            }   
            currentPoint += distanceBetweenPoints;
        }
    };
    
    function _drawJoints(datasource, which, color)
    {   
        var context = this.config.context,
            i = 0,
            len = datasource.length,
            dotSize = 4,
            currentPoint    = (dotSize / 2) + 15,
            distanceBetweenPoints = (this.config.sizes.width -30) / len,
            height = this.config.sizes.height,
            yPos, prevYPos = 0;

        for( ; i < len; i++, yPos = 0)
        {                   

            if(datasource[i])
            {
                yPos = height * (1-datasource[i][which]);
            }       

            if(prevYPos > 0 && yPos > 0)
            {
                context.beginPath();
                context.lineWidth = 1;
                context.strokeStyle = color;
                context.fillStyle = color;
                context.shadowColor = "rgba( 0, 0, 0, 0.3 )";
                context.shadowOffsetX = 1;
                context.shadowOffsetY = 1;
                context.shadowBlur = 3;
                context.moveTo(currentPoint - distanceBetweenPoints- (dotSize / 2), prevYPos);
                context.lineTo(currentPoint - (dotSize / 2), yPos);
                context.stroke();
                context.closePath();
            }

            prevYPos = yPos;
            currentPoint += distanceBetweenPoints;
        }            
    };
    
    function _drawRange(datasource, which, color)
    {
        
        var context = this.config.context,
            dotSize = 4, 
            i = 0, 
            len = datasource.length,
            distanceBetweenPoints = (this.config.sizes.width -30) / len,
            yPos,
            height = this.config.sizes.height,
            currentPoint = (dotSize / 2) + 15;

        if(datasource[i])
        {
            yPos = height * (1-datasource[i]["max"][which]);
        }     
            
        context.beginPath();
        context.fillStyle = color;
        context.opacity = .4;
        context.shadowColor = "rgba( 0, 0, 0, 0.3 )";
        context.shadowOffsetX = 1;
        context.shadowOffsetY = 1;
        context.shadowBlur = 3;
        context.moveTo(currentPoint - distanceBetweenPoints- (dotSize / 2), yPos);
                            
        for( ; i < len; i++, yPos = 0)
        {
            if(datasource[i])
            {
                yPos = height * (1-datasource[i]["max"][which]);
            }
            
            if(yPos)
            {
                context.lineTo(currentPoint - (dotSize / 2), yPos);    
            }        
            currentPoint += distanceBetweenPoints;
        }
                
        for( ; i > 0; i--, yPos = 0)
        {
            if(datasource[i])
            {
                yPos = height * (1-datasource[i]["min"][which]); 
            }
                  
            if(yPos)
            {
                context.lineTo(currentPoint - (dotSize / 2), yPos);   
            }
                    
            currentPoint -= distanceBetweenPoints;
        }
        
        
        context.fill();
        context.closePath();
        
        /*
        var context = this.config.context,
            i = 0,
            len = datasource["min"].length,
            dotSize = 4,
            currentPoint    = (dotSize / 2) + 15,
            distanceBetweenPoints = (this.config.sizes.width -30) / len,
            height = this.config.sizes.height,
            yPos, yPosMax, prevYPos = 0, prevYMaxPos = 0;

        for( ; i < len; i++, yPos = 0, yPosMax = 0)
        {
            if(datasource[i])
            {
                yPos = height * (1-datasource["min"][i][which]);
                yPosMax = height * (1-datasource["max"][i][which]);
            }       

            if(prevYPos > 0 && yPos > 0)
            {
                context.beginPath();
                context.moveTo(currentPoint - distanceBetweenPoints- (dotSize / 2), prevYPos);
                context.lineTo(currentPoint - (dotSize / 2), yPos);
                context.lineWidth = 1;
                context.strokeStyle = color;
                context.stroke();
            }

            prevYPos = yPos;
            currentPoint += distanceBetweenPoints;
        }   */
    }
    
    
})();
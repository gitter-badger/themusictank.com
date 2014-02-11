<?php if(Configure::read('debug') < 1) : ?>
    <script>
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-1624062-1']);
        _gaq.push(['_trackPageview']);

        (function() {
          var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        //ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
          ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
          var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();

        window.onerror = function(message, file, line) { 
           var sFormattedMessage = '[' + file + ' (' + line + ')] ' + message; 
           _gaq.push(['_trackEvent', 'Exceptions', 'Application', sFormattedMessage, null, true]);
        };
  </script>    
<?php endif; ?>
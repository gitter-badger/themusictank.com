<?php

class DisqusHelper extends AppHelper {
            
    public function get($identifier, $title)
    {   
        return " 
        <div id=\"disqus_thread\"></div>
        <script type=\"text/javascript\">
            (function() {
                var disqus_identifier = '".$identifier."';       
                var disqus_title = '".$title." &mdash; ". __("The Music Tank") ."';
                var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                dsq.src = '//themusictank.disqus.com/embed.js';
                (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
            })();
        </script>
        ";
    }        
}
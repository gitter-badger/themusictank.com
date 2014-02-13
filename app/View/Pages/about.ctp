<div class="aboutus">
    
    <section class="introduction">
        <article>    
            <h2><?php echo __("About The Music Tank"); ?></h2>

            <p>We want to give you an easy and fun way to rate tracks and albums accurately. By integrating with various 
            music streaming service, we aspire to collect opinions on the music you listen to.</p>

            <p>First launched on the 30th of December 1999, The Music Tank has always been around under a form or another. Based
                in <a href="https://goo.gl/maps/7NQ1r" rel="nofollow" target="_blank">Montreal</a>, our team is made of music enthusiasts that have day jobs 
                which can be a little less motivating than what we want to achieve here.</p>
        </article>
    </section>


        <article>
        <h3><?php echo __("Meet the team"); ?></h3>


        <ul>
            <li>
                <img src="https://si0.twimg.com/profile_images/3354801626/921b36d3a93bce4904532760df133fd0.jpeg" class="thumbnail" alt="Francois Faubert" />
                <h4>Francois Faubert</h4> 
                <ul> 
                    <li><?php echo $this->Html->link(__("@francoisfaubert"), "http://www.twitter.com/francoisfaubert"); ?></li>
                    <li><?php echo $this->Html->link(__("Profile"), array("controller" => "users", "action" => "view", "francois")); ?></li>
                </ul>
                <blockquote>            
                    <p>I have started TMT way back in 1999, mainly because I wanted a 
                    sandbox where I could play and learn how to build websites. How have things changed since then, 
                    whether in our personal lives, in the music industry or on the Internet itself.</p>

                    <p>TMT had many lives along the way, but there is one moment that I remember particularly fondly.
                    <a href="http://web.archive.org/web/20030128044725/http://www.themusictank.com/" target="_blank">In 2003</a>, 
                    the website became a place where people would hang out and became less content focused. We didn't have 
                    any pretension of being able to compete in quality and quantity of content against the myriad of other music
                    websites and we just began to have fun. We were a community - albeit a small one - and we'd spend whole days
                    hanging out on the message boards.</p>

                    <p>Hopefully you are one of the few who might remember this era. If not no worries: you are now part of this
                    bright new one.</p>

                    <p>The idea behind this iteration of TMT first came to me while I was painstakingly writing a review for
                    a band I did not particularly enjoy. English not being my first language, I felt like what I was writing 
                    was bland and imprecise and the whole process was not an enjoyable one.</p>

                    <p>I thought of making a reviewing tool that would automatically and accurately measure what I thought of
                    a record without having to write it down.</p>

                    <p>Web-based technology has evolved and so has my developer skill set. I have built what the tool I wanted, but
                    decided that your pooled opinions was more important than ours.</p>

                    <p>Hopefully you will enjoy the time you spend here. I means a lot to me to see you.</p>

                    <p>Thanks for coming!</p>            
                </blockquote>        
            </li>
            <li>
                <img src="https://si0.twimg.com/profile_images/107322449/chevreuiljlucJorion.jpg" class="thumbnail" alt="Julien Guay" />
                <h4>Julien Guay</h4> 
                <ul> 
                    <li><?php echo $this->Html->link(__("@julienguay"), "http://www.twitter.com/julienguay"); ?></li>
                    <li><?php echo $this->Html->link(__("Profile"), array("controller" => "users", "action" => "view", "julien")); ?></li>
                </ul>
                <blockquote>[...]</blockquote>        
            </li>    
        </ul>

    </article>

</div>
    
<?php /* Bunch of stuff to remember / grive credit to 
http://developer.echonest.com/
http://rdio.com/
https://github.com/aadsm/JavaScript-ID3-Reader

 hidden soundcloud player : http://grandbuda.com/#
 
 */ ?>
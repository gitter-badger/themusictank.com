
<section class="jumbotron colored introduction">
	<div class="container">
	    <h1><?php echo __("About Us"); ?></h1>
	    <p>We want to give you an easy and fun way to rate tracks and albums accurately. By integrating with various music streaming service, we aspire to collect opinions on the music you listen to.</p>
	    <p>First launched on the 30th of December 1999, The Music Tank has always been around under a form or another</p>
	    <p>The Music Tank is a team of 2 music enthusiasts who work together during our free time to build a place where people come hang out and discuss music.</p>
    </div>
</section>

<section class="map">
    <article>
        * Based in <a href="https://goo.gl/maps/7NQ1r" rel="nofollow" target="_blank">Montreal</a>
    </article>
    <div id="mapCanvas"></div>
</section>

<div class="container container-fluid team">

    <h2><?php echo __("Meet the team"); ?></h2>
    <p class="lead">Follow us on TMT to have an idea of our musical taste.</p>

	<?php if(isset($francois) && count($francois)) : ?>
    <div class="row">
    	<div class="col-md-3">
    		<?php echo $this->element("userbadge", array("user" => $francois)); ?>
	    </div>
	    <div class="col-md-9">

	    	<h4>Welcome!</h4>

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

	        <p>Web-based technology has evolved and so has my developer skill set. I have finanly built the tool I wanted, but
	        in the end, your pooled opinions are more important than mine. This is why I've built a website around it.</p>

	        <p>Hopefully you will enjoy the time you spend here. It means a lot to me to see you.</p>

	        <p>Thanks for coming!</p>

	        <p><a href="http://www.twitter.com/francoisfaubert">~Francois Faubert</a></p>
	    </div>
	</div>
	<?php endif; ?>


	<?php if(isset($julien) && count($julien)) : ?>
    <div class="row">
    	<div class="col-md-3">
    		<?php echo $this->element("userbadge", array("user" => $julien)); ?>
	    </div>
	    <div class="col-md-9">
	        <p><a href="http://www.twitter.com/julienguay">~Julien Guay</p>
	    </div>
	</div>
	<?php endif; ?>

	<div class="row">
		<div class="col-md-12">
		    <h2><?php echo __("Thanks"); ?></h2>
		    <p class="lead">Check out the following services which have, in some way, helped us get up and running.</p>
			<ul>
				<li><a href="http://gethiphop.net/">Hip Hop</a></li>
			</ul>
		</div>	
	</div>

</div>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>

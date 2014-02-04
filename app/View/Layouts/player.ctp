<?php if (!$this->Session->check('Auth.User')) { die("Unauthorized"); }  ?>
<!DOCTYPE html>
<html>
<head>	
	<title><?php echo ($title_for_layout) ? $title_for_layout . " &mdash; " : ""; ?><?php echo __("The Music Tank"); ?></title>
    <?php 
        echo $this->element('head', array(
            "customMetas" => array(
                $this->Html->script(array('player/reviewer'))
               // $this->Html->css(array('lib/animations', 'player')),
               // $this->Html->script(array('//code.jquery.com/ui/1.10.3/jquery-ui.js', 'lib/jquery.ui.touch-punch.min', 'lib/animation/RequestAnimationFrame', 'reviewer/player'))
            )
        )); 
        echo  $this->Html->script(array('player/reviewer');
    ?>    
</head>
<body class="player loading">
    
    <?php echo $this->element('header'); ?>
    
    <section>
        
        <?php echo $this->Session->flash(); ?>

        <div id="player">        
            <!-- Statuses -->
            <div class="animated statuses label_starpowering">
                <strong>x 4</strong> <?php echo __('Starpowering'); ?>!
            </div>                
            <div class="animated statuses label_suckpowering">
                <strong>x -4</strong> <?php echo __('Suckpowering'); ?>!
            </div>

            <!-- Multiplier -->
            
            <div class="animated multiplier label_multiplier_-3">
                <strong>x -3</strong> <?php echo __("multiplier"); ?>
            </div>
            <div class="animated multiplier label_multiplier_-2">
                <strong>x -2</strong> <?php echo __("multiplier"); ?>
            </div>
            <div class="animated multiplier label_multiplier_-1">
                <strong>x -1</strong> <?php echo __("multiplier"); ?>
            </div>
            <div class="animated multiplier label_multiplier_1">
                <strong>x 1</strong> <?php echo __("multiplier"); ?>
            </div>
            <div class="animated multiplier label_multiplier_2">
                <strong>x 2</strong> <?php echo __("multiplier"); ?>
            </div>
            <div class="animated multiplier label_multiplier_3">
                <strong>x 3</strong> <?php echo __("multiplier"); ?>
            </div>      

            <div class="multiplier-progress">
                <div id="slice">
                    <div class="pie"></div>
                    <div class="pie fill"></div>                        
                </div>
            </div>
            
            <div class="groove"></div>
        </div>

        <div id="controls">

            <div class="seek">            
                <div class="bar"><div class="progress"><span class="knob"></span></div></div>
                <span class="time"></span>
            </div>

            <div class="trackinfo">
                <span class="image"></span>
                <span class="title"></span>
                <span class="artist"></span>            
                <span class="album"></span>
            </div>

            <button id="play" name="play" type="button">Play</button>
            <button id="stop" name="stop" type="button">Stop</button>
            <?php echo $this->Html->link($artist["name"], array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?>
            <?php echo $this->Html->link($album["name"], array('controller' => 'albums', 'action' => 'view', $album["slug"])); ?>
            <?php echo $this->Html->link($track["title"], array('controller' => 'tracks', 'action' => 'view', $track["slug"])); ?>
            
        </div>
     
        <?php echo $this->element('reviewer'); ?>
        <?php echo $this->fetch('content'); ?>    

        <div class="dlg focusLost" title="<?php echo __('Must retain focus'); ?>">
            <p><?php echo __('This window needs to always stay active and in focus for the reviewing process to work, sorry :s'); ?></p>
            
        </div>        
        
        <div id="focusLost">
            <div class="aid">
                <h3><?php echo __("Must retain focus"); ?></h3>
                <p><?php echo __('This window needs to always stay active and in focus for the reviewing process to work, sorry :s'); ?></p>
                <button name="resume"><?php echo __('Resume') ?></button>
                <?php echo $this->Html->link(__("Back to artist page"), array('controller' => 'artists', 'action' => 'view', $artist["slug"])); ?>
            </div>
        </div>        

        <div class="mask loading-mask">
            <div class="icon">Loading</div>
        </div>

    </section>
            
    <?php echo $this->element('analytics'); ?>                
    <?php echo $this->element('sql_dump'); ?>
</body>
</html>

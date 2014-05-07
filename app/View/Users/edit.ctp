<div class="container">
    <?php echo $this->Session->flash('auth'); ?>

    <h2><?php echo __("Edit your profile"); ?></h2>

    <?php echo $this->Form->create(); ?>
    <?php echo $this->Form->input("id"); ?>

    <div class="row">
        <div class="col-md-6">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo __("The Music Tank profile url"); ?></h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <?php echo $this->Form->input("slug", array("label" => "http://www.themusictank.com/profiles/view/")); ?>
                    </div>
                    <p><?php echo __("Once you save, we'll check if the url is available. Eventually, I'll code something better and less frustrating ~ Frank"); ?></p>
                </div>
            </div>

        </div>

        <div class="col-md-6">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo __("Player preferences"); ?></h3>
                </div>
                <div class="panel-body">
                    <p><?php echo __("When available, we will use your prefered player to play songs. When your choice is not available, we will give you the option to use mp3 files."); ?></p>
                    <div class="form-group">
                        <?php echo $this->Form->input("preferred_player_api", array("options" => $availableApis)); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-6">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo __("Additional information"); ?></h3>
                </div>
                <div class="panel-body">
                    <p><?php echo __("You don't have to fill the following optional fields to review tracks on The Music Tank, but it does make this place a little more lively if you take some time to describe yourself."); ?></p>    
                    <div class="form-group">
                        <?php echo $this->Form->input("firstname"); ?>
                        <?php echo $this->Form->input("lastname"); ?>
                        <?php echo $this->Form->input("location"); ?>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-6">
            &nbsp;
        </div>  
    </div>

    <?php echo $this->Form->end(__("Update profile settings")); ?>
</div>


<div class="container">
    
    <h2><?php echo __("Manage third party services"); ?></h2>

    <div class="row">

        <div class="col-md-6">

             <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo __("Rdio"); ?></h3>
                </div>
                <?php if($hasRdio) : ?>   
                    <div class="panel-body">     
                        <?php if(!$hasFacebook && !$hasAccount) : ?>
                            <?php echo __("Rdio being your only active authentification method, we cannot revoke it."); ?>
                        <?php else : ?>
                            <?php echo $this->Html->link("Disconnect your Rdio account", array('controller' => 'users', 'action' => 'disconnectRdio'), array("class" => "btn btn-danger btn-block")); ?>
                        <?php endif; ?>
                    </div>
               <?php endif; ?>

                <?php if(!$hasRdio) : ?>  
                <ul class="list-group">
                    <li class="list-group-item"><?php echo $this->Html->link(__("Connect a Rdio account"), array('controller' => 'apis', 'action' => 'connectRdio', '?' => array("rurl" => $redirectUrl)), array("class" => "btn btn-info btn-lg btn-block")); ?></li>
                </ul>
                <?php endif; ?>
            </div>

        </div>
        <div class="col-md-6">

             <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo __("Facebook"); ?></h3>
                </div>
                <?php if($hasFacebook) : ?>      
                    <div class="panel-body">             
                        <?php if(!$hasRdio && !$hasAccount) : ?>
                            <?php echo __("Facebook being your only active authentification method, we cannot revoke it."); ?>
                        <?php else : ?>
                            <?php echo $this->Html->link("Disconnect your Facebook account", array('controller' => 'users', 'action' => 'disconnectFacebook'), array("class" => "btn btn-danger btn-block")); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if(!$hasFacebook) : ?>  
                <ul class="list-group">
                    <li class="list-group-item"><?php echo $this->Html->link(__("Connect a Facebook account"), array('controller' => 'apis', 'action' => 'connectFacebook'), array("class" => "btn btn-primary btn-lg btn-block")); ?></li>
                </ul>
                <?php endif; ?>
            </div>

        </div>
    </div>

</div>
<div class="container container-fluid">
    <?php echo $this->Session->flash('auth'); ?>

    <h2><?php echo __("Access your account"); ?></h2>

    <div class="row">
        <div class="col-md-7">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo __("Authentication"); ?></h3>
                </div>
                <div class="panel-body">
                    <p><?php echo __("Connecting The Music Tank with Rdio or Facebook will automatically give you access to this website. If you have accounts on these, you have an account on TMT."); ?></p>
                </div>
                <ul class="list-group">
                    <li class="list-group-item"><?php echo $this->Html->link(__("Login with Rdio"), array('controller' => 'apis', 'action' => 'connectRdio', '?' => array("rurl" => $redirectUrl)), array("class" => "btn btn-info btn-lg btn-block")); ?></li>
                    <li class="list-group-item"><?php echo $this->Html->link('<i class="fa fa-facebook-square"></i> ' . __("Login with Facebook"), array('controller' => 'apis', 'action' => 'connectFacebook', '?' => array("rurl" => $redirectUrl)), array('escape' => false, "class" => "btn btn-primary btn-lg btn-block")); ?></li>
                </ul>
            </div>

        </div>

        <div class="col-md-5">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo __("If you do not use any of these services..."); ?></h3>
                </div>
                <div class="panel-body">
                    <p><?php echo __("Connect with your existing TMT account"); ?></p>      

                    <?php echo $this->Form->create('User', array('action' => 'login', 'role' => 'form')); ?>
                        <div class="form-group">
                            <?php echo $this->Form->input('username', array("label" => __("Email"))); ?>
                        </div>
                        <div class="form-group">
                            <?php echo $this->Form->input('password'); ?>
                        </div>
                    <?php echo $this->Form->end(__('Login')); ?>

                </div>
                <ul class="list-group">
                    <li class="list-group-item"><?php echo $this->Html->link( '<span class="fa fa-certificate"></span> ' . __("Create a new TMT account"), array('controller' => 'users', 'action' => 'create', '?' => array("rurl" => $redirectUrl)), array('escape' => false)); ?></li>
                </ul>
            </div>

        </div>
    </div>

</div>
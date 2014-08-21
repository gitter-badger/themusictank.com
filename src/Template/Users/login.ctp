<div class="header-wrapper plain">
    <i class="mask"></i>
</div>

<article class="container container-fluid static">

    <header class="collapsed"></header>

    <div class="row content headerless">

        <h1><?= __("Profiles"); ?></h1>

        <?= $this->Flash->render(); ?>

         <h2><?= __("Access your account"); ?></h2>

        <div class="col-md-7">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= __("Authentication"); ?></h3>
                </div>
                <div class="panel-body">
                    <p><?= __("Connecting The Music Tank with these social services will automatically give you access to this website. If you have accounts on these, you have an account on TMT."); ?></p>
                </div>
                <ul class="list-group">
                <?php /*
                    <li class="list-group-item"><?= $this->Html->link(__("Login with Rdio"), array('controller' => 'apis', 'action' => 'connectRdio', '?' => array("rurl" => $redirectUrl)), array("class" => "btn btn-info btn-lg btn-block")); ?></li>
                    */ ?>
                    <li class="list-group-item"><?= $this->Html->link('<i class="fa fa-facebook-square"></i> ' . __("Login with Facebook"), ['controller' => 'facebookusers', 'action' => 'connect', '?' => ["rurl" => $redirectUrl]], ['escape' => false, "class" => "btn btn-primary btn-lg btn-block"]); ?></li>
                </ul>
            </div>

        </div>

        <div class="col-md-5">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= __("If you do not use any of these services..."); ?></h3>
                </div>
                <div class="panel-body">
                    <p><?= __("Connect with your existing TMT account"); ?></p>

                    <?= $this->Form->create('Users', array('action' => 'login', 'role' => 'form', 'class' => 'form-horizontal')); ?>
                        <div class="form-group">
                            <?= $this->Form->input('username', ["label" => __("Email")]); ?>
                        </div>
                        <div class="form-group">
                            <?= $this->Form->input('password'); ?>
                        </div>
                    <?= $this->Form->button(__('Login')); ?>
                    <?= $this->Form->end(); ?>

                </div>
                <ul class="list-group">
                    <li class="list-group-item"><?= $this->Html->link(__("Create a new TMT account"), ['controller' => 'users', 'action' => 'add', '?' => ["rurl" => $redirectUrl]]); ?></li>
                </ul>
            </div>

        </div>

    </div>
</article>

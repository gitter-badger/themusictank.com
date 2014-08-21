<div class="header-wrapper plain">
    <i class="mask"></i>
</div>

<article class="container container-fluid static">

    <header class="collapsed"></header>

    <div class="row content headerless">
        <div class="col-md-12">

        <h1><?= __("Profiles"); ?></h1>

            <?= $this->Flash->render(); ?>

            <h2><?= __("Create a new account"); ?></h2>

            <?= $this->Form->create('User'); ?>
                <?= $this->Form->input('username', array("label" => __("Email"))); ?>
                <?= $this->Form->input('firstname'); ?>
                <?= $this->Form->input('lastname'); ?>
                <?= $this->Form->input('password'); ?>
                <?= $this->Form->input('password_confirm', array("type"=>"password")); ?>
                <?= $this->Form->button(__('Create')); ?>
            <?= $this->Form->end(); ?>

        </div>

    </div>
</article>

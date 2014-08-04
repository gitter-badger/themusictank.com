<div class="header-wrapper plain">
    <i class="mask"></i>
</div>

<article class="container container-fluid static">

    <header class="collapsed"></header>

    <div class="row content headerless">
        <h1><?php echo __("Profiles"); ?></h1>

        <div class="col-md-12">

            <h2><?php echo __("Create a new account"); ?></h2>

            <?php echo $this->Form->create('User'); ?>
                <?php echo $this->Form->input('username', array("label" => __("Email"))); ?>
                <?php echo $this->Form->input('firstname'); ?>
                <?php echo $this->Form->input('lastname'); ?>
                <?php echo $this->Form->input('password'); ?>
                <?php echo $this->Form->input('password_confirm', array("type"=>"password")); ?>
            <?php echo $this->Form->end(__('Create')); ?>

        </div>

    </div>
</article>

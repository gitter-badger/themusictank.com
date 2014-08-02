<div class="header-wrapper">
    <section class="jumbotron introduction">
        <div class="container">
            <h1><?php echo __("Tanker Profiles"); ?></h1>
        </div>
    </section>
</div>

<div class="container container-fluid">
    <div class="row">
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
</div>

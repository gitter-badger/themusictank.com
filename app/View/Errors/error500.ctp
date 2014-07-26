<div class="header-wrapper">
    <section class="jumbotron introduction">
        <div class="container">
            <h1><?php echo __d('cake', 'Error'); ?></h1>
            <p class="lead"><?php echo __d('cake', 'An Internal Error Has Occurred.'); ?></p>
        </div>
    </section>
</div>

<div class="container container-fluid">
    <div class="row">
        <div class="md-col-12">
        <?php if (Configure::read('debug') > 0): ?>
            <?php echo $this->element('exception_stack_trace'); ?>
        <?php else : ?>
            <script>$.post('/ajax/bugreport/', {'type': '500', 'where': window.location.pathname, 'user_id': null}, $.noop);</script>
            <p>There is an important issue that breaks this page and it cannot be rendered.</p>
            <p>I have been automatically notified and I will fix it real soon.</p>
            <p>~ Francois</p>
        <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <p style="font-size:8em; text-align:center;"><i class="fa fa-bug"></i></p>
    </div>
</div>
